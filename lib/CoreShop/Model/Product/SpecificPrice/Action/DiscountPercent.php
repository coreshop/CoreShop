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

namespace CoreShop\Model\Product\SpecificPrice\Action;

use CoreShop\Model\Product;
use Pimcore\Model;

class DiscountPercent extends AbstractAction
{

    /**
     * @var int
     */
    public $currency_id;

    /**
     * @var int
     */
    public $percent;

    /**
     * @var string
     */
    public $type = "discountPercent";

    /**
     * @return mixed
     */
    public function getCurrencyId()
    {
        return $this->currency_id;
    }

    /**
     * @param mixed $currency_id
     */
    public function setCurrencyId($currency_id)
    {
        $this->currency_id = $currency_id;
    }

    /**
     * @return int
     */
    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * @param int $percent
     */
    public function setPercent($percent)
    {
        $this->percent = $percent;
    }

    /**
     * Calculate discount
     *
     * @param float $basePrice
     * @param Product $product
     * @return float
     */
    public function getDiscount($basePrice, Product $product)
    {
        return $basePrice * ($this->getPercent() / 100);
    }


    /**
     * get new price for product
     *
     * @param Product $product
     * @return float|boolean $price
     */
    public function getPrice(Product $product)
    {
        return false;
    }
}
