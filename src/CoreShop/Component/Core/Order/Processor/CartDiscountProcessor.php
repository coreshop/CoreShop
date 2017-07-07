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
use CoreShop\Component\Order\Cart\Calculator\CartDiscountCalculatorInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;

final class CartDiscountProcessor implements CartProcessorInterface
{
    /**
     * @var CartDiscountCalculatorInterface
     */
    private $cartDiscountCalculator;

    /**
     * @param CartDiscountCalculatorInterface $cartDiscountCalculator
     */
    public function __construct(CartDiscountCalculatorInterface $cartDiscountCalculator)
    {
        $this->cartDiscountCalculator = $cartDiscountCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function process(CartInterface $cart)
    {
        $cart->setDiscount($this->cartDiscountCalculator->getDiscount($cart, true), true);
        $cart->setDiscount($this->cartDiscountCalculator->getDiscount($cart, false), false);
    }
}