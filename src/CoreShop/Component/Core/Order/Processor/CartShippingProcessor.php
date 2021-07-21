<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

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
    private TaxedShippingCalculatorInterface $carrierPriceCalculator;
    private ShippableCarrierValidatorInterface $carrierValidator;
    private DefaultCarrierResolverInterface $defaultCarrierResolver;
    private AddressProviderInterface $defaultAddressProvider;
    private AdjustmentFactoryInterface $adjustmentFactory;
    private CartContextResolverInterface $cartContextResolver;

    public function __construct(
        TaxedShippingCalculatorInterface $carrierPriceCalculator,
        ShippableCarrierValidatorInterface $carrierValidator,
        DefaultCarrierResolverInterface $defaultCarrierResolver,
        AddressProviderInterface $defaultAddressProvider,
        AdjustmentFactoryInterface $adjustmentFactory,
        CartContextResolverInterface $cartContextResolver
    ) {
        $this->carrierPriceCalculator = $carrierPriceCalculator;
        $this->carrierValidator = $carrierValidator;
        $this->defaultCarrierResolver = $defaultCarrierResolver;
        $this->defaultAddressProvider = $defaultAddressProvider;
        $this->adjustmentFactory = $adjustmentFactory;
        $this->cartContextResolver = $cartContextResolver;
    }

    public function process(OrderInterface $cart): void
    {
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
            $context
        );
        $priceWithoutTax = $this->carrierPriceCalculator->getPrice(
            $cart->getCarrier(),
            $cart,
            $address,
            false,
            $context
        );

        $cart->addAdjustment(
            $this->adjustmentFactory->createWithData(
                AdjustmentInterface::SHIPPING,
                '',
                $priceWithTax,
                $priceWithoutTax
            )
        );
    }

    /**
     * @param OrderInterface   $cart
     * @param AddressInterface $address
     */
    private function resolveDefaultCarrier(OrderInterface $cart, AddressInterface $address)
    {
        if (!$cart instanceof CoreOrderInterface) {
            return;
        }

        try {
            $cart->setCarrier($this->defaultCarrierResolver->getDefaultCarrier($cart, $address));
        } catch (UnresolvedDefaultCarrierException $ex) {
        }
    }
}
