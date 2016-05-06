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

namespace CoreShop\Model\Cart\PriceRule\Condition;

use CoreShop\Model\Cart\PriceRule;
use CoreShop\Model\Cart;
use Pimcore\Model;

class TotalAvailable extends AbstractCondition
{
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

    /**
     * Check if Cart is Valid for Condition
     *
     * @param Cart $cart
     * @param PriceRule $priceRule
     * @param bool|false $throwException
     * @return bool
     * @throws \Exception
     */
    public function checkCondition(Cart $cart, PriceRule $priceRule, $throwException = false)
    {
        //Check Total Available
        if ($this->getTotalAvailable() > 0) {
            if ($this->getTotalUsed() >= $this->getTotalAvailable()) {
                if ($throwException) {
                    throw new \Exception("This voucher has already been used");
                } else {
                    return false;
                }
            }
        }

        return true;
    }
}
