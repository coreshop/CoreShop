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

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Bundle\OrderBundle\Form\Type\CartCreationType;
use CoreShop\Component\Address\Formatter\AddressFormatterInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Pimcore\DataObject\InheritanceHelper;
use CoreShop\Component\Store\Model\StoreInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractCartCreationController extends AbstractSaleController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function getCustomerDetailsAction(Request $request)
    {
        $this->isGrantedOr403();

        $customerId = $request->get('customerId');
        $customer = $this->get('coreshop.repository.customer')->find($customerId);

        if (!$customer instanceof CustomerInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => "Customer with ID '$customerId' not found"]);
        }

        return $this->viewHandler->handle(['success' => true, 'customer' => $this->getDataForObject($customer)]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function salePreviewAction(Request $request)
    {
        $cart = $this->get('coreshop.factory.cart')->createNew();
        $form = $this->get('form.factory')->createNamed('', CartCreationType::class, $cart, [
            'customer' => $request->get('customer'),
        ]);

        if ($request->getMethod() === 'POST') {
            $handledForm = $form->handleRequest($request);

            $cart = $handledForm->getData();

            InheritanceHelper::useInheritedValues(function() use ($cart) {
                $this->get('coreshop.cart_processor')->process($cart);
            }, true);

            $json = $this->getCartDetails($cart);

            return $this->viewHandler->handle(['success' => true, 'data' => $json]);
        }

        return $this->viewHandler->handle(['success' => false, 'message' => 'Method not supported, use POST']);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function saleCreationAction(Request $request)
    {
        $this->isGrantedOr403();

        $cart = $this->get('coreshop.factory.cart')->createNew();
        $form = $this->get('form.factory')->createNamed('', CartCreationType::class, $cart, [
            'customer' => $request->get('customer'),
        ]);

        if ($request->getMethod() === 'POST') {
            $handledForm = $form->handleRequest($request);

            if (!$handledForm->isValid()) {
                return $this->viewHandler->handle(
                    [
                        'success' => false,
                        'message' => $this->get('coreshop.resource.helper.form_error_serializer')->serializeErrorFromHandledForm($form),
                    ]
                );
            }

            $cart = $handledForm->getData();

            InheritanceHelper::useInheritedValues(function() use ($cart) {
                $this->get('coreshop.cart_processor')->process($cart);
            }, true);

            $saleResponse = $this->persistCart($cart);

            return $this->viewHandler->handle($saleResponse);
        }

        return $this->viewHandler->handle(['success' => false, 'message' => 'Method not supported, use POST']);
    }

    /**
     * @param CartInterface $cart
     *
     * @return array
     */
    protected function getCartDetails(CartInterface $cart)
    {
        $jsonCart = $this->getDataForObject($cart);

        $jsonCart['o_id'] = $cart->getId();
        $jsonCart['customer'] = $cart->getCustomer() instanceof CustomerInterface ? $this->getDataForObject($cart->getCustomer()) : null;
        $jsonCart['items'] = $this->getItemDetails($cart);
        $jsonCart['currency'] = $this->getCurrency($cart->getCurrency() ?: $cart->getStore()->getCurrency());
        $jsonCart['baseCurrency'] = $this->getCurrency($cart->getStore()->getCurrency());
        $jsonCart['store'] = $cart->getStore() instanceof StoreInterface ? $this->getStore($cart->getStore()) : null;

        $jsonCart['address'] = [
            'shipping' => $this->getDataForObject($cart->getShippingAddress()),
            'billing' => $this->getDataForObject($cart->getInvoiceAddress()),
        ];

        if ($cart->getShippingAddress() instanceof AddressInterface && $cart->getShippingAddress()->getCountry() instanceof CountryInterface
        ) {
            $jsonCart['address_shipping_formatted'] = $this->getAddressFormatter()->formatAddress($cart->getShippingAddress());
        } else {
            $jsonCart['address_shipping_formatted'] = '';
        }

        if ($cart->getInvoiceAddress() instanceof AddressInterface && $cart->getInvoiceAddress()->getCountry() instanceof CountryInterface) {
            $jsonCart['address_billing_formatted'] = $this->getAddressFormatter()->formatAddress($cart->getInvoiceAddress());
        } else {
            $jsonCart['address_billing_formatted'] = '';
        }

        $totals = $this->getCartSummary($cart);

        foreach ($totals as &$totalEntry) {
            $price = $totalEntry['value'];
            $priceConverted = $this->get('coreshop.currency_converter')->convert($price, $cart->getStore()->getCurrency()->getIsoCode(), $cart->getCurrency()->getIsoCode());
            $totalEntry['value'] = $priceConverted;
        }

        unset($totalEntry);

        $jsonCart['summary'] = $totals;

        return $jsonCart;
    }

    /**
     * @param CartInterface $cart
     *
     * @return array
     */
    protected function getItemDetails(CartInterface $cart)
    {
        $items = [];

        foreach ($cart->getItems() as $item) {
            if ($item instanceof CartItemInterface) {
                $items[] = $this->prepareCartItem($cart, $item);
            }
        }

        return $items;
    }

    /**
     * @param CartItemInterface $item
     *
     * @return array
     */
    protected function prepareCartItem(CartInterface $cart, CartItemInterface $item)
    {
        $currentCurrency = $cart->getCurrency()->getIsoCode();
        $currency = $cart->getStore()->getCurrency()->getIsoCode();

        $moneyConverter = $this->get('coreshop.currency_converter');
        $moneyFormatter = $this->get('coreshop.money_formatter');

        $price = $item->getItemPrice();
        $total = $item->getTotal();
        $basePrice = $moneyConverter->convert($price, $currentCurrency, $currency);
        $baseTotal = $moneyConverter->convert($total, $currentCurrency, $currency);

        return [
            'product' => $item->getProduct() ? $item->getProduct()->getId() : 0,
            'productName' => $item->getProduct() ? $item->getProduct()->getName() : '',
            'quantity' => $item->getQuantity(),
            'basePrice' => $price,
            'baseTotal' => $total,
            'price' => $basePrice,
            'total' => $baseTotal,
        ];
    }

    /**
     * @param CartInterface $cart
     *
     * @return array
     */
    protected function getCartSummary(CartInterface $cart)
    {
        return [
            [
                'key' => 'subtotal',
                'value' => $cart->getSubtotal(true),
            ],
            [
                'key' => 'subtotal_tax',
                'value' => $cart->getSubtotalTax(),
            ],
            [
                'key' => 'subtotal_without_tax',
                'value' => $cart->getSubtotal(false),
            ],
            [
                'key' => 'discount_without_tax',
                'value' => -1 * $cart->getDiscount(false),
            ],
            [
                'key' => 'discount_tax',
                'value' => -1 * $cart->getDiscount(true) - $cart->getDiscount(false),
            ],
            [
                'key' => 'discount',
                'value' => -1 * $cart->getDiscount(true),
            ],
            [
                'key' => 'total_without_tax',
                'value' => $cart->getTotal(false),
            ],
            [
                'key' => 'total_tax',
                'value' => $cart->getTotalTax(),
            ],
            [
                'key' => 'total',
                'value' => $cart->getTotal(true),
            ],
        ];
    }

    /**
     * @param CurrencyInterface $currency
     *
     * @return array
     */
    protected function getCurrency(CurrencyInterface $currency)
    {
        return [
            'name' => $currency->getName(),
            'symbol' => $currency->getSymbol(),
        ];
    }

    /**
     * @param StoreInterface $store
     *
     * @return array
     */
    protected function getStore(StoreInterface $store)
    {
        return [
            'id' => $store->getId(),
            'name' => $store->getName(),
        ];
    }

    /**
     * @return AddressFormatterInterface
     */
    private function getAddressFormatter()
    {
        return $this->get('coreshop.address.formatter');
    }

    /**
     * @param CartInterface $cart
     *
     * @return mixed
     */
    abstract protected function persistCart(CartInterface $cart);
}
