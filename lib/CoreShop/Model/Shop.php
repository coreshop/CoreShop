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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model;
use CoreShop\Exception;
use Pimcore\Model\Site;

/**
 * Class Shop
 * @package CoreShop\Model
 */
class Shop extends AbstractModel {

    /**
     * @var int
     */
    public $id;

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
     * @return mixed
     * @throws Exception
     * @throws \Exception
     *
     * @returns Shop
     */
    public static function getShop() {
        if(Configuration::multiShopEnabled()) {
            if (Site::isSiteRequest()) {
                $site = Site::getCurrentSite();

                return self::getShopForSite($site);
            }
        }

        return self::getDefaultShop();
    }

    /**
     * @param Site $site
     * @return mixed
     * @throws Exception
     *
     * @returns Shop
     */
    public static function getShopForSite(Site $site) {
        $data = self::getList()->setCondition("siteId = ?", array($site->getId()))->getData();

        if(count($data) > 1) {
            throw new Exception("More that one shop for this site is configured!");
        }

        if(count($data) === 0) {
            throw new Exception("No shop for this site is configured!");
        }

        return $data[0];
    }

    /**
     * Returns the default Shop
     *
     * @return mixed
     * @throws Exception
     *
     * @returns Shop
     */
    public static function getDefaultShop() {
        $data = self::getList()->setCondition("isDefault = 1")->getData();

        if(count($data) > 1) {
            throw new Exception("More that one default shop is configured!");
        }

        if(count($data) === 0) {
            throw new Exception("No default shop is configured!");
        }

        return $data[0];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
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