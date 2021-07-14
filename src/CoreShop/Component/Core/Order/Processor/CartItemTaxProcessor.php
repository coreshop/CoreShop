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

use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Product\ProductTaxCalculatorFactoryInterface;
use CoreShop\Component\Core\Provider\AddressProviderInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Collector\TaxCollectorInterface;
use Pimcore\Model\DataObject\Fieldcollection;
use Webmozart\Assert\Assert;

final class CartItemTaxProcessor implements CartProcessorInterface
{
    private ProductTaxCalculatorFactoryInterface $productTaxFactory;
    private TaxCollectorInterface $taxCollector;
    private AddressProviderInterface $defaultAddressProvider;

    public function __construct(
        ProductTaxCalculatorFactoryInterface $productTaxFactory,
        TaxCollectorInterface $taxCollector,
        AddressProviderInterface $defaultAddressProvider
    ) {
        $this->productTaxFactory = $productTaxFactory;
        $this->taxCollector = $taxCollector;
        $this->defaultAddressProvider = $defaultAddressProvider;
    }

    public function process(OrderInterface $cart): void
    {
        $store = $cart->getStore();

        /**
         * @var StoreInterface $store
         */
        Assert::isInstanceOf($store, StoreInterface::class);

        /**
         * @var OrderItemInterface $item
         */
        foreach ($cart->getItems() as $item) {
            $taxCalculator = $this->productTaxFactory->getTaxCalculator($item->getProduct(),
                $cart->getShippingAddress() ?: $this->defaultAddressProvider->getAddress($cart)
            );

            $fieldCollection = new Fieldcollection();

            if ($taxCalculator instanceof TaxCalculatorInterface) {
                foreach ($item->getUnits() as $unit) {
                    $unitFieldCollection = new Fieldcollection();

                    if ($store->getUseGrossPrice()) {
                        $unitFieldCollection->setItems(
                            $this->taxCollector->collectTaxesFromGross($taxCalculator, $unit->getTotal(true))
                        );
                    } else {
                        $unitFieldCollection->setItems(
                            $this->taxCollector->collectTaxes($taxCalculator, $unit->getTotal(false))
                        );
                    }

                    $unit->setTaxes($unitFieldCollection);

                    $fieldCollection->setItems(
                        $this->taxCollector->mergeTaxes(
                            $unitFieldCollection->getItems(),
                            $fieldCollection->getItems()
                        )
                    );
                }
            }

            $item->setTaxes($fieldCollection);
        }
    }
}
