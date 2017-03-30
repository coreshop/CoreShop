<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreShopLegacyBundle\Model;

use CoreShop\Bundle\CoreShopLegacyBundle\Exception;
use Pimcore\Cache;
use Pimcore\Logger;
use Pimcore\Model\Document;
use Pimcore\Model\Site;
use Pimcore\Tool;

/**
 * Class Shop
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Model
 */
class Shop extends AbstractModel
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $template;

    /**
     * @var boolean
     */
    public $isDefault;

    /**
     * @var int
     */
    public $siteId;

    /**
     * @return Shop
     * @throws Exception
     * @throws \Exception
     *
     * @returns Shop
     */
    public static function getShop()
    {
        if (Configuration::multiShopEnabled()) {
            if (Site::isSiteRequest()) {
                $site = Site::getCurrentSite();

                return self::getShopForSite($site);
            } else {
                if (Tool::isFrontentRequestByAdmin()) {
                    $document = self::getNearestDocumentByPath(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

                    if ($document instanceof Document) {
                        do {
                            try {
                                $site = Site::getByRootId($document->getId());

                                if ($site instanceof Site) {
                                    return self::getShopForSite($site);
                                }
                            } catch (\Exception $x) {
                            }

                            $document = $document->getParent();
                        } while ($document instanceof Document);
                    }
                }
            }
        }

        return self::getDefaultShop();
    }

    /**
     * @note: Copied from Pimcore\Controller\Router\Route\Frontend, probably needs to be changed sometimes
     *
     * @param string|false $path
     * @param bool $ignoreHardlinks
     * @param array $types
     * @return Document|Document\PageSnippet|null|string
     */
    protected static function getNearestDocumentByPath($path, $ignoreHardlinks = false, $types = [])
    {
        $document = null;
        $pathes[] = "/";
        $pathParts = explode("/", $path);
        $tmpPathes = [];
        foreach ($pathParts as $pathPart) {
            $tmpPathes[] = $pathPart;
            $t = implode("/", $tmpPathes);
            if (!empty($t)) {
                $pathes[] = $t;
            }
        }

        $pathes = array_reverse($pathes);

        foreach ($pathes as $p) {
            if ($document = Document::getByPath($p)) {
                if (empty($types) || in_array($document->getType(), $types)) {
                    break;
                }
            } elseif (Site::isSiteRequest()) {
                // also check for a pretty url in a site
                $site = Site::getCurrentSite();
                $documentService = new Document\Service();

                // undo the changed made by the site detection in self::match()
                $originalPath = preg_replace("@^" . $site->getRootPath() . "@", "", $p);

                $sitePrettyDocId = $documentService->getDocumentIdByPrettyUrlInSite($site, $originalPath);
                if ($sitePrettyDocId) {
                    if ($sitePrettyDoc = Document::getById($sitePrettyDocId)) {
                        $document = $sitePrettyDoc;
                        break;
                    }
                }
            }
        }


        if ($document) {
            if (!$ignoreHardlinks) {
                if ($document instanceof Document\Hardlink) {
                    if ($hardLinkedDocument = Document\Hardlink\Service::getNearestChildByPath($document, $path)) {
                        $document = $hardLinkedDocument;
                    } else {
                        $document = Document\Hardlink\Service::wrap($document);
                    }
                }
            }

            return $document;
        }

        return null;
    }

    /**
     * @param Site $site
     * @return mixed
     * @throws Exception
     *
     * @returns Shop
     */
    public static function getShopForSite(Site $site)
    {
        $cacheKey = "coreshop_shop_site_" . $site->getId();

        try {
            $object = \Zend_Registry::get($cacheKey);
            if (!$object) {
                throw new Exception($cacheKey.' in registry is null');
            }

            return $object;
        } catch (\Exception $e) {
            try {
                if (!$object = Cache::load($cacheKey)) {
                    $data = self::getList()->setCondition("siteId = ?", [$site->getId()])->getData();

                    if (count($data) > 1) {
                        throw new Exception("More that one shop for this site is configured!");
                    }

                    if (count($data) === 0) {
                        throw new Exception("No shop for this site is configured!");
                    }

                    $object = $data[0];

                    \Zend_Registry::set($cacheKey, $object);
                    Cache::save($object, $cacheKey);
                } else {
                    \Zend_Registry::set($cacheKey, $object);
                }

                return $object;
            } catch (\Exception $e) {
                Logger::warning($e->getMessage());
            }
        }

        return static::getDefaultShop();
    }

    /**
     * Returns the default Shop
     *
     * @return mixed
     * @throws Exception
     *
     * @returns Shop
     */
    public static function getDefaultShop()
    {
        $cacheKey = "coreshop_shop_site_default";

        try {
            $object = \Zend_Registry::get($cacheKey);
            if (!$object) {
                throw new Exception($cacheKey.' in registry is null');
            }

            return $object;
        } catch (\Exception $e) {
            try {
                if (!$object = Cache::load($cacheKey)) {
                    $data = self::getList()->setCondition("isDefault = 1")->getData();

                    if (count($data) > 1) {
                        throw new Exception("More that one default shop is configured!");
                    }

                    if (count($data) === 0) {
                        throw new Exception("No default shop is configured!");
                    }

                    $object = $data[0];

                    \Zend_Registry::set($cacheKey, $object);
                    Cache::save($object, $cacheKey);
                } else {
                    \Zend_Registry::set($cacheKey, $object);
                }

                return $object;
            } catch (\Exception $e) {
                Logger::warning($e->getMessage());
            }
        }

        throw new Exception("No default shop is configured!");
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("%s (%s)", $this->getName(), $this->getId());
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return boolean
     */
    public function getIsDefault()
    {
        return $this->isDefault;
    }

    /**
     * @param boolean $isDefault
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;
    }

    /**
     * @return int
     */
    public function getSiteId()
    {
        return $this->siteId;
    }

    /**
     * @param int $siteId
     */
    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;
    }
}
