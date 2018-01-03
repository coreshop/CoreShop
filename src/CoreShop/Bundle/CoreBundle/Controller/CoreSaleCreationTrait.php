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

namespace CoreShop\Bundle\CoreBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ViewHandlerInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

trait CoreSaleCreationTrait
{
    /**
     * @var ViewHandlerInterface
     */
    protected $viewHandler;

    public function getCarrierDetailsAction(Request $request)
    {
        $productIds = $request->get("products");
        $customerId = $request->get("customer");
        $shippingAddressId = $request->get("shippingAddress");
        $invoiceAddressId = $request->get("invoiceAddress");
        $storeId = $request->get('store');

        /**
         * @var $currency CurrencyInterface
         */
        $currency = $this->get('coreshop.repository.currency')->find($request->get("currency"));

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

        $this->get('coreshop.context.store.fixed')->setStore($store);
        $this->get('coreshop.context.currency.fixed')->setCurrency($currency);
        $this->get('coreshop.context.customer.fixed')->setCustomer($customer);
        $this->get('coreshop.context.country.fixed')->setCountry($shippingAddress->getCountry());

        /**
         * @var $cart \CoreShop\Component\Core\Model\CartInterface
         */
        $cart = $this->createTempCart($customer, $shippingAddress, $invoiceAddress, $currency, $productIds);
        $this->get('coreshop.cart.manager')->persistCart($cart);

        $carriers = $this->get('coreshop.carrier.discovery')->discoverCarriers($cart, $cart->getShippingAddress());

        $currentCurrency = $this->get('coreshop.context.currency')->getCurrency()->getIsoCode();

        /**
         * @var $carrier CarrierInterface
         */
        foreach ($carriers as $carrier) {
            $price = $this->get('coreshop.carrier.price_calculator.taxed')->getPrice($carrier, $cart, $cart->getShippingAddress());
            $priceConverted = $this->get('coreshop.currency_converter')->convert($price, $currentCurrency, $currency->getIsoCode());
            $priceFormatted = $this->get('coreshop.money_formatter')->format($priceConverted, $currency->getIsoCode());

            $result[] = [
                'id' => $carrier->getId(),
                'name' => $carrier->getName(),
                'price' => $price,
                'priceFormatted' => $priceFormatted
            ];
        }

        $cart->delete();

        return $this->viewHandler->handle(['success' => true, 'carriers' => $result]);
    }

    protected function getTotalArray(CartInterface $cart)
    {
        /**
         * @var $cart \CoreShop\Component\Core\Model\CartInterface
         */
        Assert::isInstanceOf($cart, \CoreShop\Component\Core\Model\CartInterface::class);

        $result = parent::getTotalArray($cart);

        array_splice($result, 3, 0, [
            [
                'key' => 'shipping_without_tax',
                'value' => $cart->getShipping(false)
            ],
            [
                'key' => 'shipping_tax',
                'value' => $cart->getShipping(true) - $cart->getShipping(false)
            ],
            [
                'key' => 'shipping',
                'value' => $cart->getShipping(true)
            ]
        ]);

        return $result;
    }

    protected function prepareCart(Request $request, CartInterface $cart)
    {
        /**
         * @var $cart \CoreShop\Component\Core\Model\CartInterface
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
