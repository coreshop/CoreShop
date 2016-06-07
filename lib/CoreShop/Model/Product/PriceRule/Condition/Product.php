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

use CoreShop\Model\Product\AbstractProductPriceRule;
use CoreShop\Model\Product\PriceRule;

class Product extends AbstractCondition
{
    /**
     * @var int
     */
    public $product;

    /**
     * @var string
     */
    public $type = 'product';

    /**
     * @return \CoreShop\Model\Product
     */
    public function getProduct()
    {
        if (!$this->product instanceof \CoreShop\Model\Product) {
            $this->product = \CoreShop\Model\Product::getByPath($this->product);
        }

        return $this->product;
    }

    /**
     * @param int $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * Check if Product is Valid for Condition.
     *
     * @param \CoreShop\Model\Product $product
     * @param AbstractProductPriceRule $priceRule
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function checkCondition( \CoreShop\Model\Product $product, AbstractProductPriceRule $priceRule)
    {
        return $this->getProduct() instanceof \CoreShop\Model\Product ? $this->getProduct()->getId() == $product->getId() : false;
    }
}
