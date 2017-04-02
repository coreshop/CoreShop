<?php

namespace CoreShop\Bundle\StoreBundle\Locator;

use CoreShop\Bundle\ConfigurationBundle\Helper\ConfigurationHelperInterface;
use CoreShop\Bundle\StoreBundle\Helper\PimcoreSiteHelperInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Store\Model\Store;
use Pimcore\Cache;
use Pimcore\Logger;
use Pimcore\Model\Document;
use Pimcore\Model\Site;
use Pimcore\Tool;

class StoreLocator implements StoreLocatorInterface {

    /**
     * @var ConfigurationHelperInterface
     */
    private $configurationHelper;

    /**
     * @var PimcoreSiteHelperInterface
     */
    private $pimcoreSiteHelper;

    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @param ConfigurationHelperInterface $configurationHelper
     * @param PimcoreSiteHelperInterface $pimcoreSiteHelper
     * @param RepositoryInterface $repository
     */
    public function __construct(
        ConfigurationHelperInterface $configurationHelper,
        PimcoreSiteHelperInterface $pimcoreSiteHelper,
        RepositoryInterface $repository
    )
    {
        $this->configurationHelper = $configurationHelper;
        $this->pimcoreSiteHelper = $pimcoreSiteHelper;
        $this->repository = $repository;
    }


    /**
     * @throws \Exception
     * @return Store
     */
    public function getStore()
    {
        if ($this->configurationHelper->isMultiStoreEnabled()) {
            if ($this->pimcoreSiteHelper->isSiteRequest()) {
                $site = $this->pimcoreSiteHelper->getCurrentSite();

                return $this->getStoreForSite($site);
            } else {
                if (Tool::isFrontentRequestByAdmin()) {
                    $document = $this->getNearestDocumentByPath(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

                    if ($document instanceof Document) {
                        do {
                            try {
                                $site = Site::getByRootId($document->getId());

                                if ($site instanceof Site) {
                                    return $this->getStoreForSite($site);
                                }
                            } catch (\Exception $x) {
                            }

                            $document = $document->getParent();
                        } while ($document instanceof Document);
                    }
                }
            }
        }

        return $this->getDefaultStore();
    }

    /**
     * @note: Copied from Pimcore\Controller\Router\Route\Frontend, probably needs to be changed sometimes
     *
     * @param string|false $path
     * @param bool $ignoreHardlinks
     * @param array $types
     * @return Document|Document\PageSnippet|null|string
     */
    private function getNearestDocumentByPath($path, $ignoreHardlinks = false, $types = [])
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
     * @throws \Exception
     *
     * @returns Store
     */
    public function getStoreForSite(Site $site)
    {
        $cacheKey = "coreshop_store_site_" . $site->getId();

        try {
            $object = \Zend_Registry::get($cacheKey);
            if (!$object) {
                throw new \Exception($cacheKey.' in registry is null');
            }

            return $object;
        } catch (\Exception $e) {
            try {
                if (!$object = Cache::load($cacheKey)) {
                    $list = $this->repository->getList();
                    $data = $list->setCondition("siteId = ?", [$site->getId()])->getData();

                    if (count($data) > 1) {
                        throw new \Exception("More that one store for this site is configured!");
                    }

                    if (count($data) === 0) {
                        throw new \Exception("No store for this site is configured!");
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

        return static::getDefaultStore();
    }

    /**
     * Returns the default store
     *
     * @return mixed
     * @throws \Exception
     *
     * @returns Site
     */
    public function getDefaultStore()
    {
        $cacheKey = "coreshop_site_site_default";

        try {
            $object = \Zend_Registry::get($cacheKey);
            if (!$object) {
                throw new \Exception($cacheKey.' in registry is null');
            }

            return $object;
        } catch (\Exception $e) {
            try {
                if (!$object = Cache::load($cacheKey)) {
                    $list = $this->repository->getList();
                    $data = $list->setCondition("isDefault = 1")->getData();

                    if (count($data) > 1) {
                        throw new \Exception("More that one default store is configured!");
                    }

                    if (count($data) === 0) {
                        throw new \Exception("No default store is configured!");
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

        throw new \Exception("No default store is configured!");
    }
}