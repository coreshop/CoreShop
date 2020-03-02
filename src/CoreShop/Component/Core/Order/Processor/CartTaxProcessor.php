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

namespace CoreShop\Component\Core\Order\Processor;

use CoreShop\Component\Core\Model\Carrier;
use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Registry\ServiceRegistry;
use CoreShop\Component\Core\Provider\AddressProviderInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Shipping\Taxation\TaxCalculationStrategyInterface;
use CoreShop\Component\Taxation\Collector\TaxCollectorInterface;
use Pimcore\Model\DataObject\Fieldcollection;

final class CartTaxProcessor implements CartProcessorInterface
{
    /**
     * @var TaxCollectorInterface
     */
    private $taxCollector;

    /**
     * @var AddressProviderInterface
     */
    private $defaultAddressProvider;

    /**
     * @var ServiceRegistry
     */
    private $registry;

    /**
     * @param TaxCollectorInterface         $taxCollector
     * @param AddressProviderInterface      $defaultAddressProvider
     * @param ServiceRegistry               $shippingTaxCalculationServices
     */
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
    public function process(CartInterface $cart)
    {
        $cart->setTaxes(null);

        $usedTaxes = [];

        /**
         * @var CartItemInterface $item
         */
        foreach ($cart->getItems() as $item) {
            $usedTaxes = $this->taxCollector->mergeTaxes($item->getTaxes() instanceof Fieldcollection ? $item->getTaxes()->getItems() : [], $usedTaxes);
        }

        $usedTaxes = $this->collectShippingTaxes($cart, $usedTaxes);

        $fieldCollection = new Fieldcollection();
        $fieldCollection->setItems($usedTaxes);
        $cart->setTaxes($fieldCollection);
    }

    /**
     * @param CartInterface $cart
     * @param array         $usedTaxes
     *
     * @return array
     */
    private function collectShippingTaxes(CartInterface $cart, array $usedTaxes = [])
    {
        if (!$cart instanceof \CoreShop\Component\Core\Model\CartInterface) {
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
            $cartTax = $taxCalculationService->calculateShippingTax($cart, $carrier, $address, $cart->getShipping(false));

            if (1 === count($cartTax)) {
                $cart->setShippingTaxRate(reset($cartTax)->getRate());
            }
            else {
                //We'll use the combined tax-rate here. The actual tax-rate is not important here anyway, since we store all detailed tax
                //information on the cart anyway
                $cart->setShippingTaxRate(round(100 * $cart->getShippingTax() / $cart->getShipping(false), 2));
            }

            $usedTaxes = $this->taxCollector->mergeTaxes($cartTax, $usedTaxes);
        }

        return $usedTaxes;
    }
}
