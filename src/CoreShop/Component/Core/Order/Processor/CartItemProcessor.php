<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Order\Processor;

use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Core\Product\TaxedProductPriceCalculatorInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;

final class CartItemProcessor implements CartProcessorInterface
{
    /**
     * @var TaxedProductPriceCalculatorInterface
     */
    private $productPriceCalculator;

    /**
     * @param TaxedProductPriceCalculatorInterface $productPriceCalculator
     */
    public function __construct(TaxedProductPriceCalculatorInterface $productPriceCalculator)
    {
        $this->productPriceCalculator = $productPriceCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function process(CartInterface $cart)
    {
        /**
         * @var $item CartItemInterface
         */
        foreach ($cart->getItems() as $item) {
            $itemNetPrice = $this->productPriceCalculator->getPrice($item->getProduct(), false);
            $itemGrossPrice = $this->productPriceCalculator->getPrice($item->getProduct(), true);
            $itemTax = $itemGrossPrice - $itemNetPrice;

            $item->setItemPrice($itemNetPrice, false);
            $item->setItemPrice($itemGrossPrice, true);
            $item->setItemRetailPrice($this->productPriceCalculator->getRetailPrice($item->getProduct(), false), false);
            $item->setItemRetailPrice($this->productPriceCalculator->getRetailPrice($item->getProduct(), true), true);
            $item->setItemWholesalePrice($item->getProduct()->getWholesalePrice());
            $item->setItemTax($itemTax);
            $item->save();
        }
    }
}