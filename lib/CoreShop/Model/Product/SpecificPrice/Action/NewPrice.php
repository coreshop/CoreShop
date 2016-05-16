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
namespace CoreShop\Model\Product\SpecificPrice\Action;

use CoreShop\Model\Product;

class NewPrice extends AbstractAction
{
    /**
     * @var float
     */
    public $newPrice;

    /**
     * @var string
     */
    public $type = 'newPrice';

    /**
     * @return float
     */
    public function getNewPrice()
    {
        return $this->newPrice;
    }

    /**
     * @param float $newPrice
     */
    public function setNewPrice($newPrice)
    {
        $this->newPrice = $newPrice;
    }

    /**
     * Calculate discount.
     *
     * @param float   $basePrice
     * @param Product $product
     *
     * @return float
     */
    public function getDiscount($basePrice, Product $product)
    {
        return 0;
    }

    /**
     * get new price for product.
     *
     * @param Product $product
     *
     * @return float $price
     */
    public function getPrice(Product $product)
    {
        return $this->getNewPrice();
    }
}
