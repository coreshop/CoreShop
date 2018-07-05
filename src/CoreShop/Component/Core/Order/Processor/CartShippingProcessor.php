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

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\CartInterface as CoreCartInterface;
use CoreShop\Component\Core\Provider\AddressProviderInterface;
use CoreShop\Component\Core\Shipping\Calculator\TaxedShippingCalculatorInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Shipping\Exception\UnresolvedDefaultCarrierException;
use CoreShop\Component\Shipping\Resolver\DefaultCarrierResolverInterface;
use CoreShop\Component\Shipping\Validator\ShippableCarrierValidatorInterface;

final class CartShippingProcessor implements CartProcessorInterface
{
    /**
     * @var TaxedShippingCalculatorInterface
     */
    private $carrierPriceCalculator;

    /**
     * @var ShippableCarrierValidatorInterface
     */
    private $carrierValidator;

    /**
     * @var DefaultCarrierResolverInterface
     */
    private $defaultCarrierResolver;

    /**
     * @var AddressProviderInterface
     */
    private $defaultAddressProvider;

    /**
     * @param TaxedShippingCalculatorInterface   $carrierPriceCalculator
     * @param ShippableCarrierValidatorInterface $carrierValidator
     * @param DefaultCarrierResolverInterface    $defaultCarrierResolver
     * @param AddressProviderInterface           $defaultAddressProvider
     */
    public function __construct(
        TaxedShippingCalculatorInterface $carrierPriceCalculator,
        ShippableCarrierValidatorInterface $carrierValidator,
        DefaultCarrierResolverInterface $defaultCarrierResolver,
        AddressProviderInterface $defaultAddressProvider
    ) {
        $this->carrierPriceCalculator = $carrierPriceCalculator;
        $this->carrierValidator = $carrierValidator;
        $this->defaultCarrierResolver = $defaultCarrierResolver;
        $this->defaultAddressProvider = $defaultAddressProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function process(CartInterface $cart)
    {
        if (!$cart instanceof \CoreShop\Component\Core\Model\CartInterface) {
            return;
        }

        $address = $cart->getShippingAddress() ?: $this->defaultAddressProvider->getAddress($cart);

        if (null === $address) {
            return;
        }

        if ($cart->getCarrier() instanceof CarrierInterface) {
            if (!$this->carrierValidator->isCarrierValid($cart->getCarrier(), $cart, $address)) {
                $cart->setCarrier(null);
                $cart->setShipping(0, true);
                $cart->setShipping(0, false);
            }
        }

        if (null === $cart->getCarrier()) {
            $this->resolveDefaultCarrier($cart, $address);

            if (null === $cart->getCarrier()) {
                return;
            }
        }

        $priceWithTax = $this->carrierPriceCalculator->getPrice($cart->getCarrier(), $cart, $address, true);
        $priceWithoutTax = $this->carrierPriceCalculator->getPrice($cart->getCarrier(), $cart, $address, false);

        $cart->setShipping($priceWithTax, true);
        $cart->setShipping($priceWithoutTax, false);
    }

    /**
     * @param CartInterface    $cart
     * @param AddressInterface $address
     */
    private function resolveDefaultCarrier(CartInterface $cart, AddressInterface $address)
    {
        if (!$cart instanceof CoreCartInterface) {
            return;
        }

        try {
            $cart->setCarrier($this->defaultCarrierResolver->getDefaultCarrier($cart, $address));
        } catch (UnresolvedDefaultCarrierException $ex) {
        }
    }
}
