<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */

namespace CoreShop\Model\PriceRule\Condition;

use CoreShop\Model\PriceRule;
use CoreShop\Model\Cart;
use Pimcore\Model;

class TotalAvailable extends AbstractCondition {

    /**
     * @var int
     */
    public $totalAvailable;

    /**
     * @var int
     */
    public $totalUsed;

    /**
     * @var string
     */
    public $type = "totalAvailable";

    /**
     * @return int
     */
    public function getTotalAvailable()
    {
        return $this->totalAvailable;
    }

    /**
     * @param int $totalAvailable
     */
    public function setTotalAvailable($totalAvailable)
    {
        $this->totalAvailable = $totalAvailable;
    }

    /**
     * @return int
     */
    public function getTotalUsed()
    {
        return $this->totalUsed;
    }

    /**
     * @param int $totalUsed
     */
    public function setTotalUsed($totalUsed)
    {
        $this->totalUsed = $totalUsed;
    }

    public function checkCondition(Cart $cart, PriceRule $priceRule, $throwException = false)
    {
        //Check Total Available
        if($this->getTotalAvailable() > 0)
            if($this->getTotalUsed() >= $this->getTotalAvailable())
                if($throwException) throw new \Exception("This voucher has already been used"); else return false;

        return true;
    }
}
