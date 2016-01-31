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

abstract class AbstractAction extends Product\SpecificPrice\AbstractSpecificPrice
{
    /**
     * @var string
     */
    public $elementType = "action";

    /**
     * Calculate discount
     *
     * @param float $basePrice
     * @param Product $product
     * @return float
     */
    abstract public function getDiscount($basePrice, Product $product);

    /**
     * get new price for product
     *
     * @param Product $product
     * @return float $price
     */
    abstract public function getPrice(Product $product);
}
