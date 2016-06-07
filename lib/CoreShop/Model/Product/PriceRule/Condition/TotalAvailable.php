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
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Product\PriceRule\Condition;

use CoreShop\Model\Product\PriceRule;
use CoreShop\Model\Product;

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
    public $type = 'totalAvailable';

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
     * Check if Product is Valid for Condition.
     *
     * @param Product $product
     * @param Product\AbstractProductPriceRule $priceRule
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function checkCondition(Product $product, Product\AbstractProductPriceRule $priceRule)
    {
        //Check Total Available
        if ($this->getTotalAvailable() > 0) {
            if ($this->getTotalUsed() >= $this->getTotalAvailable()) {
                return false;
            }
        }

        return true;
    }
}
