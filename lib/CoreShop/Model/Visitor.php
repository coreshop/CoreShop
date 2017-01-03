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

namespace CoreShop\Model;

use CoreShop\Model\Visitor\Page;
use CoreShop\Model\Visitor\Source;
use Pimcore\Db;

/**
 * Class Visitor
 * @package CoreShop\Model
 */
class Visitor extends AbstractModel
{
    /**
     * @var int
     */
    public $shopId;

    /**
     * @var int
     */
    public $userId;

    /**
     * @var int
     */
    public $ip;

    /**
     * @var string
     */
    public $controller;

    /**
     * @var string
     */
    public $action;

    /**
     * @var string
     */
    public $module;

    /**
     * @var string
     */
    public $referrer;

    /**
     * @var int
     */
    public $creationDate;

    /**
     * Maintenance Task for cleanup
     */
    public static function maintenance()
    {
        $keepRecordsForDays = Configuration::get("SYSTEM.VISITORS.KEEP_TRACKS_DAYS");

        if ($keepRecordsForDays > 0) {
            $date = \Carbon\Carbon::now();
            $date->subDays($keepRecordsForDays);
            $timestampToDelete = $date->getTimestamp();
            $db = Db::get();

            $orderClassId = Order::classId();
            $visitor = static::create();
            $tableName = $visitor->getDao()->getTableName();

            $sql = "SELECT visitors.id FROM $tableName visitors LEFT JOIN object_store_$orderClassId orders ON orders.visitorId=visitors.id WHERE orders.oo_id IS NULL AND visitors.creationDate < $timestampToDelete";

            $entriesToDelete = $db->fetchAll($sql);

            foreach ($entriesToDelete as $entry) {
                $visitor = Visitor::getById($entry['id']);

                if ($visitor instanceof Visitor) {
                    $visitor->delete();
                }
            }
        }
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $list = Source::getList();
        $list->setCondition("visitorId = ?", [$this->getId()]);
        $list->load();

        foreach ($list as $source) {
            $source->delete();
        }

        $list = Page::getList();
        $list->setCondition("visitorId = ?", [$this->getId()]);
        $list->load();

        foreach ($list as $source) {
            $source->delete();
        }

        return parent::delete();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("%s (%s) (%s) (%s) (%s) (%s)", $this->getIp(), $this->getController(), $this->getAction(), $this->getModule(), $this->getCreationDate(), $this->getId());
    }

    /**
     * @return int
     */
    public function getShopId()
    {
        return $this->shopId;
    }

    /**
     * @param int $shopId
     */
    public function setShopId($shopId)
    {
        $this->shopId = $shopId;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return int
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param int $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param string $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param string $module
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * @return string
     */
    public function getReferrer()
    {
        return $this->referrer;
    }

    /**
     * @param string $referrer
     */
    public function setReferrer($referrer)
    {
        $this->referrer = $referrer;
    }

    /**
     * @return int
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * @param int $creationDate
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }
}
