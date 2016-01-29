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

namespace CoreShop\Model\Product\SpecificPrice\Condition;

use CoreShop\Model;

class TimeSpan extends AbstractCondition {

    /**
     * @var int
     */
    public $dateFrom;

    /**
     * @var float
     */
    public $dateTo;

    /**
     * @var string
     */
    public $type = "timeSpan";

    /**
     * @return int
     */
    public function getDateFrom()
    {
        if(!$this->dateFrom instanceof \Zend_Date)
            $this->dateFrom = new \Zend_Date($this->dateFrom / 1000);

        return $this->dateFrom;
    }

    /**
     * @param int $dateFrom
     */
    public function setDateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;
    }

    /**
     * @return float
     */
    public function getDateTo()
    {
        if(!$this->dateTo instanceof \Zend_Date)
            $this->dateTo = new \Zend_Date($this->dateTo / 1000);

        return $this->dateTo;
    }

    /**
     * @param float $dateTo
     */
    public function setDateTo($dateTo)
    {
        $this->dateTo = $dateTo;
    }

    /**
     * Check if Product is Valid for Condition
     *
     * @param Model\Product $product
     * @param Model\Product\SpecificPrice $specificPrice
     * @return boolean
     */
    public function checkCondition(Model\Product $product, Model\Product\SpecificPrice $specificPrice)
    {
        //Check Availability
        $date = \Zend_Date::now();

        if($this->getDateFrom() instanceof \Zend_Date) {
            if ($date->get(\Zend_Date::TIMESTAMP) < $this->getDateFrom()->get(\Zend_Date::TIMESTAMP)) {
                return false;
            }
        }

        if($this->getDateTo() instanceof \Zend_Date) {
            if ($date->get(\Zend_Date::TIMESTAMP) > $this->getDateTo()->get(\Zend_Date::TIMESTAMP)) {
                return false;
            }
        }

        return true;
    }
}
