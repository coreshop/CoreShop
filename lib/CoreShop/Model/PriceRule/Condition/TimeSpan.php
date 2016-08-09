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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\PriceRule\Condition;

use CoreShop\Exception;
use CoreShop\Model\Cart\PriceRule;
use CoreShop\Model\Cart;
use CoreShop\Model\Product as ProductModel;

/**
 * Class TimeSpan
 * @package CoreShop\Model\PriceRule\Condition
 */
class TimeSpan extends AbstractCondition
{
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
    public $type = 'timeSpan';

    /**
     * @return \Zend_Date
     */
    public function getDateFrom()
    {
        if (!$this->dateFrom instanceof \Zend_Date) {
            $this->dateFrom = new \Zend_Date($this->dateFrom / 1000);
        }

        return $this->dateFrom;
    }

    /**
     * @param int|\Zend_Date $dateFrom
     */
    public function setDateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;
    }

    /**
     * @return \Zend_Date
     */
    public function getDateTo()
    {
        if (!$this->dateTo instanceof \Zend_Date) {
            $this->dateTo = new \Zend_Date($this->dateTo / 1000);
        }

        return $this->dateTo;
    }

    /**
     * @param int|\Zend_Date $dateTo
     */
    public function setDateTo($dateTo)
    {
        $this->dateTo = $dateTo;
    }

    /**
     * Check if Cart is Valid for Condition.
     *
     * @param Cart       $cart
     * @param PriceRule  $priceRule
     * @param bool|false $throwException
     *
     * @return bool
     *
     * @throws Exception
     */
    public function checkConditionCart(Cart $cart, PriceRule $priceRule, $throwException = false)
    {
        return $this->check($throwException);
    }

    /**
     * Check if Product is Valid for Condition.
     *
     * @param ProductModel $product
     * @param ProductModel\AbstractProductPriceRule $priceRule
     *
     * @return bool
     */
    public function checkConditionProduct(ProductModel $product, ProductModel\AbstractProductPriceRule $priceRule)
    {
        return $this->check();
    }

    /**
     * @param bool $throwException
     * @return bool
     * @throws Exception
     */
    protected function check($throwException = false)
    {
        //Check Availability
        $date = \Zend_Date::now();

        if ($this->getDateFrom() instanceof \Zend_Date) {
            if ($date->get(\Zend_Date::TIMESTAMP) < $this->getDateFrom()->get(\Zend_Date::TIMESTAMP)) {
                if ($throwException) {
                    throw new Exception('This voucher has expired');
                } else {
                    return false;
                }
            }
        }

        if ($this->getDateTo() instanceof \Zend_Date) {
            if ($date->get(\Zend_Date::TIMESTAMP) > $this->getDateTo()->get(\Zend_Date::TIMESTAMP)) {
                if ($throwException) {
                    throw new Exception('This voucher has expired');
                } else {
                    return false;
                }
            }
        }

        return true;
    }
}
