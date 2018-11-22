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
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Product\ProductTaxCalculatorFactoryInterface;
use CoreShop\Component\Core\Provider\AddressProviderInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Collector\TaxCollectorInterface;
use Pimcore\Model\DataObject\Fieldcollection;
use Webmozart\Assert\Assert;

final class CartItemTaxProcessor implements CartProcessorInterface
{
    /**
     * @var ProductTaxCalculatorFactoryInterface
     */
    private $productTaxFactory;

    /**
     * @var TaxCollectorInterface
     */
    private $taxCollector;

    /**
     * @var AddressProviderInterface
     */
    private $defaultAddressProvider;

    /**
     * @param ProductTaxCalculatorFactoryInterface $productTaxFactory
     * @param TaxCollectorInterface                $taxCollector
     * @param AddressProviderInterface             $defaultAddressProvider
     */
    public function __construct(
        ProductTaxCalculatorFactoryInterface $productTaxFactory,
        TaxCollectorInterface $taxCollector,
        AddressProviderInterface $defaultAddressProvider
    ) {
        $this->productTaxFactory = $productTaxFactory;
        $this->taxCollector = $taxCollector;
        $this->defaultAddressProvider = $defaultAddressProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function process(CartInterface $cart)
    {
        $store = $cart->getStore();

        /**
         * @var StoreInterface $store
         */
        Assert::isInstanceOf($store, StoreInterface::class);

        /**
         * @var CartItemInterface $item
         */
        foreach ($cart->getItems() as $item) {
            $taxCalculator = $this->productTaxFactory->getTaxCalculator($item->getProduct(), $cart->getShippingAddress() ?: $this->defaultAddressProvider->getAddress($cart));

            $fieldCollection = new Fieldcollection();

            if ($taxCalculator instanceof TaxCalculatorInterface) {
                $fieldCollection->setItems($this->taxCollector->collectTaxes($taxCalculator, $item->getTotal(false)));

                if ($store->getUseGrossPrice()) {
                    $item->setItemTax($taxCalculator->getTaxesAmountFromGross($item->getItemPrice(true)));
                } else {
                    $item->setItemTax($taxCalculator->getTaxesAmount($item->getItemPrice(false)));
                }
            }

            $item->setTaxes($fieldCollection);
        }
    }
}
