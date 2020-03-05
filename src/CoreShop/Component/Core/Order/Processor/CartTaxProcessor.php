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

use CoreShop\Component\Core\Model\Carrier;
use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Core\Provider\AddressProviderInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Registry\ServiceRegistry;
use CoreShop\Component\Shipping\Taxation\TaxCalculationStrategyInterface;
use CoreShop\Component\Taxation\Collector\TaxCollectorInterface;
use Pimcore\Model\DataObject\Fieldcollection;

final class CartTaxProcessor implements CartProcessorInterface
{
    private $taxCollector;
    private $defaultAddressProvider;
    private $registry;

    public function __construct(
        TaxCollectorInterface $taxCollector,
        AddressProviderInterface $defaultAddressProvider,
        ServiceRegistry $shippingTaxCalculationServices
    ) {
        $this->taxCollector = $taxCollector;
        $this->defaultAddressProvider = $defaultAddressProvider;
        $this->registry = $shippingTaxCalculationServices;
    }

    /**
     * {@inheritdoc}
     */
    public function process(OrderInterface $cart): void
    {
        $cart->setTaxes(null);

        $usedTaxes = [];

        /**
         * @var OrderItemInterface $item
         */
        foreach ($cart->getItems() as $item) {
            $usedTaxes = $this->taxCollector->mergeTaxes($item->getTaxes() instanceof Fieldcollection ? $item->getTaxes()->getItems() : [],
                $usedTaxes);
        }

        $usedTaxes = $this->collectShippingTaxes($cart, $usedTaxes);

        $fieldCollection = new Fieldcollection();
        $fieldCollection->setItems($usedTaxes);
        $cart->setTaxes($fieldCollection);
    }

    private function collectShippingTaxes(OrderInterface $cart, array $usedTaxes = []): array
    {
        if (!$cart instanceof \CoreShop\Component\Core\Model\OrderInterface) {
            return $usedTaxes;
        }

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
            $cartTax = $taxCalculationService->calculateShippingTax($cart, $carrier, $address,
                $cart->getShipping(false));

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
