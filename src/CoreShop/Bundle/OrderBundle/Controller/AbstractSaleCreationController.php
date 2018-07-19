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

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Order\Transformer\ProposalTransformerInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Pimcore\DataObject\InheritanceHelper;
use CoreShop\Component\Store\Model\StoreInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractSaleCreationController extends AbstractSaleController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function getCustomerDetailsAction(Request $request)
    {
        $this->isGrantedOr403();

        $customerId = $request->get("customerId");
        $customer = $this->get('coreshop.repository.customer')->find($customerId);

        if (!$customer instanceof CustomerInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => "Customer with ID '$customerId' not found"]);
        }

        return $this->viewHandler->handle(['success' => true, 'customer' => $this->getDataForObject($customer)]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function getProductDetailsAction(Request $request)
    {
        $this->isGrantedOr403();

        $productIds = $request->get("products");
        /**
         * @var $currency CurrencyInterface
         */
        $currency = $this->get('coreshop.repository.currency')->find($request->get("currency"));
        $store = $this->get('coreshop.repository.store')->find($request->get('store'));
        $customer = $this->get('coreshop.repository.customer')->find($request->get('customer'));

        if (!$customer instanceof CustomerInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => "Customer with ID '".$request->get('customer')."' not found"]);
        }

        if (!$store instanceof StoreInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => "Store with ID '".$request->get('store')."' not found"]);
        }

        if (!$currency instanceof CurrencyInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => "Currency with ID '".$request->get('currency')."' not found"]);
        }

        $this->get('coreshop.context.store.fixed')->setStore($store);
        $this->get('coreshop.context.customer.fixed')->setCustomer($customer);

        $result = [];

        $currentCurrency = $this->get('coreshop.context.currency')->getCurrency()->getIsoCode();

        foreach ($productIds as $productObject) {
            $productId = $productObject['id'];

            $product = $this->get('coreshop.repository.stack.purchasable')->find($productId);

            if ($product instanceof PurchasableInterface) {
                $result[] = InheritanceHelper::useInheritedValues(function() use ($product, $productObject, $currentCurrency, $currency) {
                    $productFlat = $this->getDataForObject($product);

                    $productFlat['quantity'] = $productObject['quantity'] ? $productObject['quantity'] : 1;

                    $price = $this->get('coreshop.product.taxed_price_calculator')->getPrice($product, true);
                    $priceFormatted = $this->get('coreshop.money_formatter')->format($price, $currentCurrency);

                    $priceConverted = $this->get('coreshop.currency_converter')->convert($price, $currentCurrency, $currency->getIsoCode());
                    $priceConvertedFormatted = $this->get('coreshop.money_formatter')->format($priceConverted, $currency->getIsoCode());

                    $productFlat['price'] = $price;
                    $productFlat['priceFormatted'] = $priceFormatted;
                    $productFlat['priceConverted'] = $priceConverted;
                    $productFlat['priceConvertedFormatted'] = $priceConvertedFormatted;

                    $total = $price * $productObject['quantity'];
                    $totalFormatted = $this->get('coreshop.money_formatter')->format($total, $currentCurrency);

                    $totalConverted = $this->get('coreshop.currency_converter')->convert($total, $currentCurrency, $currency->getIsoCode());
                    $totalConvertedFormatted = $this->get('coreshop.money_formatter')->format($totalConverted, $currency->getIsoCode());

                    $productFlat['total'] = $total;
                    $productFlat['totalFormatted'] = $totalFormatted;
                    $productFlat['totalConverted'] = $totalConverted;
                    $productFlat['totalConvertedFormatted'] = $totalConvertedFormatted;

                    return $productFlat;
                });
            }
        }

        return $this->viewHandler->handle(['success' => true, 'products' => $result]);
    }

    public function getTotalsAction(Request $request)
    {
        $this->isGrantedOr403();

        $language = $request->get('language');
        $productIds = $request->get('products');
        $customerId = $request->get('customer');
        $shippingAddressId = $request->get('shippingAddress');
        $invoiceAddressId = $request->get('invoiceAddress');
        $storeId = $request->get('store');

        /**
         * @var $currency CurrencyInterface
         */
        $currency = $this->get('coreshop.repository.currency')->find($request->get("currency"));

        $customer = $this->get('coreshop.repository.customer')->find($customerId);
        $shippingAddress = $this->get('coreshop.repository.address')->find($shippingAddressId);
        $invoiceAddress = $this->get('coreshop.repository.address')->find($invoiceAddressId);
        $store = $this->get('coreshop.repository.store')->find($storeId);

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

        $this->get('coreshop.context.store.fixed')->setStore($store);
        $this->get('coreshop.context.currency.fixed')->setCurrency($currency);
        $this->get('coreshop.context.customer.fixed')->setCustomer($customer);
        $this->get('coreshop.context.country.fixed')->setCountry($shippingAddress->getCountry());

        $cart = InheritanceHelper::useInheritedValues(function() use ($customer, $shippingAddress, $invoiceAddress, $currency, $language, $productIds, $request) {
            $cart = $this->createTempCart($customer, $shippingAddress, $invoiceAddress, $currency, $language, $productIds);

            try {
                $this->prepareCart($request, $cart);
            } catch (\InvalidArgumentException $ex) {
                return $this->viewHandler->handle(['success' => false, 'message' => $ex->getMessage()]);
            }

            $this->get('coreshop.cart_processor')->process($cart);

            return $cart;
        });

        $totals = $this->getTotalArray($cart);
        $currentCurrency = $this->get('coreshop.context.currency')->getCurrency()->getIsoCode();

        foreach ($totals as &$totalEntry) {
            $price = $totalEntry['value'];

            $priceConverted = $this->get('coreshop.currency_converter')->convert($price, $currentCurrency, $currency->getIsoCode());
            $priceFormatted = $this->get('coreshop.money_formatter')->format($priceConverted, $currency->getIsoCode());

            $totalEntry['valueFormatted'] = $priceFormatted;
        }

        return $this->viewHandler->handle(['success' => true, 'summary' => $totals]);
    }

    public function createSaleAction(Request $request)
    {
        $this->isGrantedOr403();

        $language = $request->get('language');
        $productIds = $request->get('products');
        $customerId = $request->get('customer');
        $shippingAddressId = $request->get('shippingAddress');
        $invoiceAddressId = $request->get('invoiceAddress');
        $paymentModuleName = $request->get('paymentProvider');
        $storeId = $request->get('store');

        /**
         * @var $currency CurrencyInterface
         */
        $currency = $this->get('coreshop.repository.currency')->find($request->get("currency"));

        $customer = $this->get('coreshop.repository.customer')->find($customerId);
        $shippingAddress = $this->get('coreshop.repository.address')->find($shippingAddressId);
        $invoiceAddress = $this->get('coreshop.repository.address')->find($invoiceAddressId);
        $paymentModule = $this->get('coreshop.repository.payment_provider')->find($paymentModuleName);
        $store = $this->get('coreshop.repository.store')->find($storeId);

        if (!$customer instanceof CustomerInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => "Customer with ID '$customerId' not found"]);
        }

        if (!$shippingAddress instanceof AddressInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => "Address with ID '$shippingAddressId' not found"]);
        }

        if (!$invoiceAddress instanceof AddressInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => "Address with ID '$invoiceAddressId' not found"]);
        }

        if (!$paymentModule instanceof PaymentProviderInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => "Payment Module with ID '$paymentModuleName' not found"]);
        }

        if (!$store instanceof StoreInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => "Store with ID '$storeId' not found"]);
        }

        $this->get('coreshop.context.store.fixed')->setStore($store);
        $this->get('coreshop.context.currency.fixed')->setCurrency($currency);
        $this->get('coreshop.context.customer.fixed')->setCustomer($customer);
        $this->get('coreshop.context.country.fixed')->setCountry($shippingAddress->getCountry());

        $cart = InheritanceHelper::useInheritedValues(function() use($customer, $shippingAddress, $invoiceAddress, $currency, $language, $productIds, $request, $store, $paymentModule) {
            $cart = $this->createTempCart($customer, $shippingAddress, $invoiceAddress, $currency, $language, $productIds);

            try {
                $this->prepareCart($request, $cart);
            } catch (\InvalidArgumentException $ex) {
                return $this->viewHandler->handle(['success' => false, 'message' => $ex->getMessage()]);
            }

            $cart->setStore($store);
            $cart->setPaymentProvider($paymentModule);
            $this->get('coreshop.cart_processor')->process($cart);

            return $cart;
        });

        $sale = $this->factory->createNew();
        $sale = $this->getTransformer()->transform($cart, $sale);

        $this->afterSaleCreation($sale);

        return $this->viewHandler->handle(['success' => true, 'id' => $sale->getId()]);
    }

    protected function prepareCart(Request $request, CartInterface $cart)
    {
        return $cart;
    }

    protected function getTotalArray(CartInterface $cart)
    {
        return [
            [
                'key' => 'subtotal',
                'value' => $cart->getSubtotal(true)
            ],
            [
                'key' => 'subtotal_tax',
                'value' => $cart->getSubtotalTax()
            ],
            [
                'key' => 'subtotal_without_tax',
                'value' => $cart->getSubtotal(false)
            ],
            [
                'key' => 'discount_without_tax',
                'value' => -1 * $cart->getDiscount(false)
            ],
            [
                'key' => 'discount_tax',
                'value' => -1 * $cart->getDiscount(true) - $cart->getDiscount(false)
            ],
            [
                'key' => 'discount',
                'value' => -1 * $cart->getDiscount(true)
            ],
            [
                'key' => 'total_without_tax',
                'value' => $cart->getTotal(false)
            ],
            [
                'key' => 'total_tax',
                'value' => $cart->getTotalTax()
            ],
            [
                'key' => 'total',
                'value' => $cart->getTotal(true)
            ]
        ];
    }

    protected function createTempCart(
        CustomerInterface $customer,
        AddressInterface $shippingAddress,
        AddressInterface $invoiceAddress,
        CurrencyInterface $currency,
        $localeCode,
        array $productIds
    )
    {
        /**
         * @var $cart CartInterface
         */
        $cart = $this->get('coreshop.factory.cart')->createNew();
        $cart->setParent(\Pimcore\Model\DataObject\Service::createFolderByPath('/coreshop/tmp'));
        $cart->setKey(uniqid());
        $cart->setShippingAddress($shippingAddress);
        $cart->setInvoiceAddress($invoiceAddress);
        $cart->setCurrency($currency);
        $cart->setCustomer($customer);
        $cart->setCurrency($currency);
        $cart->setLocaleCode($localeCode);

        foreach ($productIds as $productObject) {
            $productId = $productObject['id'];

            $product = $this->get('coreshop.repository.stack.purchasable')->find($productId);

            if ($product instanceof PurchasableInterface) {
                $this->get('coreshop.cart.modifier')->addItem($cart, $product, $productObject['quantity']);
            }
        }

        return $cart;
    }

    /**
     * @return ProposalTransformerInterface
     */
    protected abstract function getTransformer();

    /**
     * @param ProposalInterface $sale
     */
    protected abstract function afterSaleCreation(ProposalInterface $sale);
}
