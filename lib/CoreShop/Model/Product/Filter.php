<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Product;

use CoreShop\Model\AbstractModel;
use CoreShop\Model\Index;
use CoreShop\Model\Product\Filter\Condition\AbstractCondition;

class Filter extends AbstractModel
{
    /**
     * possible types of a condition
     * @var array
     */
    public static $availableConditions = array("select", "range", "range");

    /**
     * @param $condition
     */
    public static function addCondition($condition)
    {
        if (!in_array($condition, self::$availableConditions)) {
            self::$availableConditions[] = $condition;
        }
    }

    /**
     * @return array
     */
    public static function getConditions() {
        return self::$availableConditions;
    }

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $resultsPerPage;

    /**
     * @var string
     */
    public $order;

    /**
     * @var string
     */
    public $orderKey;

    /**
     * @var AbstractCondition[]
     */
    public $preConditions;

    /**
     * @var AbstractCondition[]
     */
    public $filters;

    /**
     * @var Index $index
     */
    public $index;

    /**
     * get Filter by ID
     *
     * @param $id
     * @return Filter|null
     */
    public static function getById($id)
    {
        return parent::getById($id);
    }

    /**
     * @return mixed
     */
    public static function getAll()
    {
        $list = new Filter\Listing();

        return $list->load();
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
     * @return int
     */
    public function getResultsPerPage()
    {
        return $this->resultsPerPage;
    }

    /**
     * @param int $resultsPerPage
     */
    public function setResultsPerPage($resultsPerPage)
    {
        $this->resultsPerPage = $resultsPerPage;
    }

    /**
     * @return string
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param string $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return mixed
     */
    public function getOrderKey()
    {
        return $this->orderKey;
    }

    /**
     * @param mixed $orderKey
     */
    public function setOrderKey($orderKey)
    {
        $this->orderKey = $orderKey;
    }

    /**
     * @return AbstractCondition[]
     */
    public function getPreConditions()
    {
        return $this->preConditions;
    }

    /**
     * @param AbstractCondition[] $preConditions
     */
    public function setPreConditions($preConditions)
    {
        $this->preConditions = $preConditions;
    }

    /**
     * @return AbstractCondition[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param AbstractCondition[] $filters
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return Index
     */
    public function getIndex()
    {
        if(!$this->index instanceof Index)
            $this->index = Index::getById($this->index);

        return $this->index;
    }

    /**
     * @param Index $index
     */
    public function setIndex($index)
    {
        if(!$index instanceof Index)
            $index = Index::getById($index);

        $this->index = $index;
    }
}