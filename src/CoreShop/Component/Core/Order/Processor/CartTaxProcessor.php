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

use CoreShop\Component\Core\Model\Carrier;
use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Provider\AddressProviderInterface;
use CoreShop\Component\Order\Cart\CartContextResolverInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Registry\ServiceRegistry;
use CoreShop\Component\Shipping\Taxation\TaxCalculationStrategyInterface;
use CoreShop\Component\Taxation\Collector\TaxCollectorInterface;
use Pimcore\Model\DataObject\Fieldcollection;

final class CartTaxProcessor implements CartProcessorInterface
{
    public function __construct(
        private TaxCollectorInterface $taxCollector,
        private AddressProviderInterface $defaultAddressProvider,
        private ServiceRegistry $registry,
        private CartContextResolverInterface $cartContextResolver,
    ) {
    }

    public function process(OrderInterface $cart): void
    {
        $context = $this->cartContextResolver->resolveCartContext($cart);
        $cart->setTaxes(null);

        $usedTaxes = [];

        /**
         * @var OrderItemInterface $item
         */
        foreach ($cart->getItems() as $item) {
            /** @psalm-suppress InvalidArgument */
            $usedTaxes = $this->taxCollector->mergeTaxes(
                $item->getTaxes() instanceof Fieldcollection ? $item->getTaxes()->getItems() : [],
                $usedTaxes,
            );
        }

        $usedTaxes = $this->collectShippingTaxes($cart, $usedTaxes, $context);

        $fieldCollection = new Fieldcollection();
        $fieldCollection->setItems($usedTaxes);
        $cart->setTaxes($fieldCollection);
    }

    private function collectShippingTaxes(OrderInterface $cart, array $usedTaxes = [], array $context = []): array
    {
        if (!$cart instanceof \CoreShop\Component\Core\Model\OrderInterface) {
            return $usedTaxes;
        }

        /**
         * @var StoreInterface $store
         */
        $store = $cart->getStore();

        if (null === $cart->getCarrier()) {
            return $usedTaxes;
        }

        $address = $cart->getShippingAddress();

        if (null === $address) {
            $address = $this->defaultAddressProvider->getAddress($cart);
        }

        if (null === $address) {
            return $usedTaxes;
        }

        $carrier = $cart->getCarrier();

        if (!$carrier instanceof Carrier) {
            return $usedTaxes;
        }

        $shippingTaxCalculationStrategy = $carrier->getTaxCalculationStrategy() ?? 'taxRule';

        if ($this->registry->has($shippingTaxCalculationStrategy)) {
            /**
             * @var TaxCalculationStrategyInterface $taxCalculationService
             */
            $taxCalculationService = $this->registry->get($shippingTaxCalculationStrategy);
            $cartTax = $taxCalculationService->calculateShippingTax($cart, $carrier, $address, $cart->getShipping($store->getUseGrossPrice()), $context);

            if (1 === count($cartTax)) {
                $cart->setShippingTaxRate(reset($cartTax)->getRate());
            } elseif (0 === $cart->getShipping(false)) {
                $cart->setShippingTaxRate(0);
            } else {
                $cart->setShippingTaxRate(round(100 * $cart->getShippingTax() / $cart->getShipping(false), 2));
            }

            $usedTaxes = $this->taxCollector->mergeTaxes($cartTax, $usedTaxes);
        }

        return $usedTaxes;
    }
}
