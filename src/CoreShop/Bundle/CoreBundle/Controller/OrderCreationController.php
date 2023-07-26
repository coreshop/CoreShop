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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Controller;

use CoreShop\Bundle\OrderBundle\Controller\OrderCreationController as BaseOrderCreationController;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Order\Cart\CartContextResolverInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Shipping\Calculator\TaxedShippingCalculatorInterface;
use CoreShop\Component\Shipping\Resolver\CarriersResolver;
use CoreShop\Component\Shipping\Resolver\CarriersResolverInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Service\Attribute\SubscribedService;
use Webmozart\Assert\Assert;

class OrderCreationController extends BaseOrderCreationController
{
    protected function prepareCartItem(OrderInterface $cart, OrderItemInterface $item): array
    {
        $itemFlat = parent::prepareCartItem($cart, $item);

        Assert::isInstanceOf($cart, \CoreShop\Component\Core\Model\OrderInterface::class);
        Assert::isInstanceOf($item, \CoreShop\Component\Core\Model\OrderItemInterface::class);

        $units = [];
        $product = $item->getProduct();

        if ($product instanceof ProductInterface && $product->hasUnitDefinitions()) {
            foreach ($product->getUnitDefinitions()->getUnitDefinitions() as $unitDefinition) {
                $units[] = [
                    'id' => $unitDefinition->getId(),
                    'name' => $unitDefinition->getUnitName(),
                ];
            }
        }

        $itemFlat['unitDefinition'] = $item->getUnitDefinition() ? $item->getUnitDefinition()->getId() : null;
        $itemFlat['unitDefinitionRecord'] = $item->getUnitDefinition() ? [
            'id' => $item->getUnitDefinition()->getId(),
            'name' => $item->getUnitDefinition()->getUnitName(),
        ] : null;
        $itemFlat['units'] = $units;

        return $itemFlat;
    }

    protected function getCartDetails(OrderInterface $cart): array
    {
        $cartDetails = parent::getCartDetails($cart);

        $cartDetails['carriers'] = $this->getCarrierDetails($cart);

        return $cartDetails;
    }

    public function getCarrierDetails(OrderInterface $cart): array
    {
        /**
         * @var \CoreShop\Component\Core\Model\OrderInterface $cart
         */
        Assert::isInstanceOf($cart, \CoreShop\Component\Core\Model\OrderInterface::class);

        if (null === $cart->getShippingAddress()) {
            return [];
        }

        $carriers = $this->container->get(CarriersResolverInterface::class)->resolveCarriers($cart, $cart->getShippingAddress());

        $result = [];

        /**
         * @var CarrierInterface $carrier
         */
        foreach ($carriers as $carrier) {
            $price = $this->container->get(TaxedShippingCalculatorInterface::class)->getPrice(
                $carrier,
                $cart,
                $cart->getShippingAddress(),
                true,
                $this->container->get(CartContextResolverInterface::class)->resolveCartContext($cart),
            );

            $result[] = [
                'id' => $carrier->getId(),
                'name' => $carrier->getIdentifier(),
                'price' => $price,
            ];
        }

        return $result;
    }

    protected function getCartSummary(OrderInterface $cart): array
    {
        /**
         * @var \CoreShop\Component\Core\Model\OrderInterface $cart
         */
        Assert::isInstanceOf($cart, \CoreShop\Component\Core\Model\OrderInterface::class);

        $result = parent::getCartSummary($cart);

        array_splice($result, 6, 0, [
            [
                'key' => 'payment_provider_fee',
                'value' => $cart->getPaymentProviderFee(),
                'convertedValue' => $cart->getConvertedPaymentProviderFee(),
            ],
            [
                'key' => 'shipping',
                'value' => $cart->getShipping(true),
                'convertedValue' => $cart->getConvertedShipping(true),
            ],
            [
                'key' => 'shipping_without_tax',
                'value' => $cart->getShipping(false),
                'convertedValue' => $cart->getConvertedShipping(false),
            ],
            [
                'key' => 'shipping_tax',
                'value' => $cart->getShipping(true) - $cart->getShipping(false),
                'convertedValue' => $cart->getConvertedShipping(true) - $cart->getConvertedShipping(false),
            ],
        ]);

        return $result;
    }

    public static function getSubscribedServices(): array
    {
        return parent::getSubscribedServices() + [
                new SubscribedService( 'CoreShop\Component\Shipping\Resolver\CarriersResolverInterface', CarriersResolver::class, attributes: new Autowire('CoreShop\Component\Shipping\Resolver\CarriersResolverInterface')),
            TaxedShippingCalculatorInterface::class,
            CartContextResolverInterface::class,
        ];
    }
}
