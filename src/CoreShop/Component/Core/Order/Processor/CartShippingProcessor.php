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

use CoreShop\Component\Core\Shipping\Calculator\TaxedShippingCalculatorInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;

final class CartShippingProcessor implements CartProcessorInterface
{
    /**
     * @var TaxedShippingCalculatorInterface
     */
    private $carrierPriceCalculator;

    /**
     * @param TaxedShippingCalculatorInterface $carrierPriceCalculator
     */
    public function __construct(TaxedShippingCalculatorInterface $carrierPriceCalculator)
    {
        $this->carrierPriceCalculator = $carrierPriceCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function process(CartInterface $cart)
    {
        if (!$cart instanceof \CoreShop\Component\Core\Model\CartInterface) {
            return;
        }

        if (null === $cart->getCarrier()) {
            return;
        }

        if (null === $cart->getShippingAddress()) {
            return;
        }

        $priceWithTax = $this->carrierPriceCalculator->getPrice($cart->getCarrier(), $cart, $cart->getShippingAddress(), true);
        $priceWithoutTax = $this->carrierPriceCalculator->getPrice($cart->getCarrier(), $cart, $cart->getShippingAddress(), false);

        $cart->setShipping($priceWithTax, true);
        $cart->setShipping($priceWithoutTax, false);
    }
}