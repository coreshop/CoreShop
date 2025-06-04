<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Core\Order\Processor;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\OrderInterface as CoreOrderInterface;
use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Core\Provider\AddressProviderInterface;
use CoreShop\Component\Order\Cart\CartContextResolverInterface;
use CoreShop\Component\Order\Factory\AdjustmentFactoryInterface;
use CoreShop\Component\Order\Model\AdjustmentInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Shipping\Calculator\TaxedShippingCalculatorInterface;
use CoreShop\Component\Shipping\Exception\UnresolvedDefaultCarrierException;
use CoreShop\Component\Shipping\Resolver\DefaultCarrierResolverInterface;
use CoreShop\Component\Shipping\Validator\ShippableCarrierValidatorInterface;

final class CartShippingProcessor implements CartProcessorInterface
{
    public function __construct(
        private TaxedShippingCalculatorInterface $carrierPriceCalculator,
        private ShippableCarrierValidatorInterface $carrierValidator,
        private DefaultCarrierResolverInterface $defaultCarrierResolver,
        private AddressProviderInterface $defaultAddressProvider,
        private AdjustmentFactoryInterface $adjustmentFactory,
        private CartContextResolverInterface $cartContextResolver,
    ) {
    }

    public function process(OrderInterface $cart): void
    {
        if ($cart->isImmutable()) {
            return;
        }

        if (!$cart instanceof CoreOrderInterface) {
            return;
        }

        $totalWeight = 0;

        /**
         * @var OrderItemInterface $item
         */
        foreach ($cart->getItems() as $item) {
            $product = $item->getProduct();

            if ($product instanceof ProductInterface) {
                $item->setItemWeight($product->getWeight());
                $item->setTotalWeight($item->getQuantity() * $product->getWeight());

                $totalWeight += $item->getWeight();
            }
        }

        $cart->setWeight($totalWeight);

        if (!$cart->hasShippableItems()) {
            $cart->setCarrier(null);

            return;
        }

        $address = $cart->getShippingAddress() ?: $this->defaultAddressProvider->getAddress($cart);

        if (null === $address) {
            return;
        }

        if ($cart->getCarrier() instanceof CarrierInterface) {
            if (!$this->carrierValidator->isCarrierValid($cart->getCarrier(), $cart, $address)) {
                $cart->setCarrier(null);
            }
        }

        if (null === $cart->getCarrier()) {
            $this->resolveDefaultCarrier($cart, $address);
        }

        if (null === $cart->getCarrier()) {
            return;
        }

        $context = $this->cartContextResolver->resolveCartContext($cart);

        $priceWithTax = $this->carrierPriceCalculator->getPrice(
            $cart->getCarrier(),
            $cart,
            $address,
            true,
            $context,
        );
        $priceWithoutTax = $this->carrierPriceCalculator->getPrice(
            $cart->getCarrier(),
            $cart,
            $address,
            false,
            $context,
        );

        $cart->addAdjustment(
            $this->adjustmentFactory->createWithData(
                AdjustmentInterface::SHIPPING,
                '',
                $priceWithTax,
                $priceWithoutTax,
            ),
        );
    }

    private function resolveDefaultCarrier(OrderInterface $cart, AddressInterface $address): void
    {
        if (!$cart instanceof CoreOrderInterface) {
            return;
        }

        try {
            $cart->setCarrier($this->defaultCarrierResolver->getDefaultCarrier($cart, $address));
        } catch (UnresolvedDefaultCarrierException) {
        }
    }
}
