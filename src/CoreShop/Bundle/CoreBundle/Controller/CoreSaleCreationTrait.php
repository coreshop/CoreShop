<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ViewHandlerInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Pimcore\DataObject\InheritanceHelper;
use CoreShop\Component\Store\Model\StoreInterface;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

trait CoreSaleCreationTrait
{
    /**
     * @var ViewHandlerInterface
     */
    protected $viewHandler;

    protected function prepareProduct(PurchasableInterface $product, $productObject, $currentCurrency, $currency, $context)
    {
        $productFlat =  parent::prepareProduct($product, $productObject, $currentCurrency, $currency, $context);

        $units = [];

        if ($product instanceof ProductInterface) {
            if ($product->hasUnitDefinitions()) {
                foreach ($product->getUnitDefinitions()->getUnitDefinitions() as $unitDefinition) {
                    $units[] = [
                        'id' => $unitDefinition->getId(),
                        'name' => $unitDefinition->getUnitName()
                    ];
                }
            }
        }

        $productFlat['units'] = $units;

        return $productFlat;
    }


    protected function createCartItem(PurchasableInterface $product, array $productObject)
    {
        return parent::createCartItem($product, $productObject);
    }

    public function getCarrierDetailsAction(Request $request)
    {
        $productIds = $request->get('products');
        $customerId = $request->get('customer');
        $shippingAddressId = $request->get('shippingAddress');
        $invoiceAddressId = $request->get('invoiceAddress');
        $storeId = $request->get('store');
        $language = $request->get('language');

        /**
         * @var CurrencyInterface $currency
         */
        $currency = $this->get('coreshop.repository.currency')->find($request->get('currency'));

        $customer = $this->get('coreshop.repository.customer')->find($customerId);
        $shippingAddress = $this->get('coreshop.repository.address')->find($shippingAddressId);
        $invoiceAddress = $this->get('coreshop.repository.address')->find($invoiceAddressId);
        $store = $this->get('coreshop.repository.store')->find($storeId);

        $result = [];

        if (!$customer instanceof CustomerInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => "Customer with ID '$customerId' not found"]);
        }

        if (!$shippingAddress instanceof AddressInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => "Address with ID '$shippingAddressId' not found"]);
        }

        if (!$invoiceAddress instanceof AddressInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => "Address with ID '$invoiceAddressId' not found"]);
        }

        if (!$store instanceof StoreInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => "Store with ID '$storeId' not found"]);
        }

        /**
         * @var \CoreShop\Component\Core\Model\CartInterface $cart
         */
        $cart = InheritanceHelper::useInheritedValues(function () use ($customer, $shippingAddress, $invoiceAddress, $currency, $language, $productIds, $store) {
            $cart = $this->createTempCart($customer, $store, $shippingAddress, $invoiceAddress, $currency, $language, $productIds);
            $this->get('coreshop.cart_processor')->process($cart);

            return $cart;
        });
        $carriers = $this->get('coreshop.carrier.resolver')->resolveCarriers($cart, $cart->getShippingAddress());

        $currentCurrency = $this->get('coreshop.context.currency')->getCurrency()->getIsoCode();

        /**
         * @var CarrierInterface $carrier
         */
        foreach ($carriers as $carrier) {
            $price = $this->get('coreshop.carrier.price_calculator.taxed')->getPrice($carrier, $cart, $cart->getShippingAddress());
            $priceConverted = $this->get('coreshop.currency_converter')->convert($price, $currentCurrency, $currency->getIsoCode());
            $priceFormatted = $this->get('coreshop.money_formatter')->format($priceConverted, $currency->getIsoCode());

            $result[] = [
                'id' => $carrier->getId(),
                'name' => $carrier->getIdentifier(),
                'price' => $price,
                'priceFormatted' => $priceFormatted,
            ];
        }

        return $this->viewHandler->handle(['success' => true, 'carriers' => $result]);
    }

    protected function getTotalArray(CartInterface $cart)
    {
        /**
         * @var \CoreShop\Component\Core\Model\CartInterface $cart
         */
        Assert::isInstanceOf($cart, \CoreShop\Component\Core\Model\CartInterface::class);

        $result = parent::getTotalArray($cart);

        array_splice($result, 3, 0, [
            [
                'key' => 'shipping_without_tax',
                'value' => $cart->getShipping(false),
            ],
            [
                'key' => 'shipping_tax',
                'value' => $cart->getShipping(true) - $cart->getShipping(false),
            ],
            [
                'key' => 'shipping',
                'value' => $cart->getShipping(true),
            ],
        ]);

        return $result;
    }

    protected function prepareCart(Request $request, CartInterface $cart)
    {
        /**
         * @var \CoreShop\Component\Core\Model\CartInterface $cart
         */
        Assert::isInstanceOf($cart, \CoreShop\Component\Core\Model\CartInterface::class);

        $carrierId = $request->get('carrier');

        if ($carrierId) {
            $carrier = $this->get('coreshop.repository.carrier')->find($carrierId);

            if (!$carrier instanceof CarrierInterface) {
                throw new \InvalidArgumentException('Carrier with ID ' . $carrierId . ' not found');
            }

            $cart->setCarrier($carrier);
        }

        return $cart;
    }
}
