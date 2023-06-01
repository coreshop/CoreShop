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

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Bundle\OrderBundle\Form\Type\CartCreationType;
use CoreShop\Bundle\ResourceBundle\Controller\PimcoreController;
use CoreShop\Bundle\ResourceBundle\Form\Helper\ErrorSerializer;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManagerInterface;
use CoreShop\Component\Address\Formatter\AddressFormatterInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Address\Model\CountryInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Customer\Repository\CustomerRepositoryInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\OrderSaleTransitions;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Pimcore\DataObject\DataLoader;
use CoreShop\Component\Pimcore\DataObject\InheritanceHelper;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OrderCreationController extends PimcoreController
{
    protected AddressFormatterInterface $addressFormatter;

    public function getCustomerDetailsAction(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
    ): Response {
        $this->isGrantedOr403();

        $customerId = $this->getParameterFromRequest($request, 'customerId');
        $customer = $customerRepository->find($customerId);

        if (!$customer instanceof CustomerInterface) {
            return $this->viewHandler->handle([
                'success' => false,
                'message' => "Customer with ID '$customerId' not found",
            ]);
        }

        return $this->viewHandler->handle(['success' => true, 'customer' => $this->getDataForObject($customer)]);
    }

    public function salePreviewAction(
        Request $request,
        FactoryInterface $orderFactory,
        FormFactoryInterface $formFactory,
        CartProcessorInterface $cartProcessor,
    ): Response {
        $cart = $orderFactory->createNew();
        $form = $formFactory->createNamed('', CartCreationType::class, $cart, [
            'customer' => $this->getParameterFromRequest($request, 'customer'),
        ]);

        if ($request->getMethod() === 'POST') {
            $handledForm = $form->handleRequest($request);

            $cart = $handledForm->getData();

            InheritanceHelper::useInheritedValues(static function () use ($cartProcessor, $cart) {
                $cartProcessor->process($cart);
            });

            $json = $this->getCartDetails($cart);

            return $this->viewHandler->handle(['success' => true, 'data' => $json]);
        }

        return $this->viewHandler->handle(['success' => false, 'message' => 'Method not supported, use POST']);
    }

    public function saleCreationAction(
        Request $request,
        FactoryInterface $orderFactory,
        FormFactoryInterface $formFactory,
        CartManagerInterface $cartManager,
        ErrorSerializer $errorSerializer,
        StateMachineManagerInterface $manager,
    ): Response {
        $this->isGrantedOr403();

        $type = $this->getParameterFromRequest($request, 'saleType', OrderSaleTransitions::TRANSITION_CART);

        $cart = $orderFactory->createNew();
        $form = $formFactory->createNamed('', CartCreationType::class, $cart, [
            'customer' => $this->getParameterFromRequest($request, 'customer'),
        ]);

        if ($request->getMethod() === 'POST') {
            $handledForm = $form->handleRequest($request);

            if (!$handledForm->isValid()) {
                return $this->viewHandler->handle(
                    [
                        'success' => false,
                        'message' => $errorSerializer->serializeErrorFromHandledForm($form),
                    ],
                );
            }

            /**
             * @var OrderInterface $cart
             */
            $cart = $handledForm->getData();
            $cart->setCreationDate(time());

            $workflow = $manager->get($cart, OrderSaleTransitions::IDENTIFIER);

            if (!$workflow->can($cart, $type)) {
                throw new HttpException(500);
            }

            InheritanceHelper::useInheritedValues(static function () use ($workflow, $cart, $type, $cartManager) {
                $workflow->apply($cart, $type);

                $cartManager->persistCart($cart);
            });

            return $this->viewHandler->handle([
                'success' => true,
                'id' => $cart->getId(),
            ]);
        }

        return $this->viewHandler->handle(['success' => false, 'message' => 'Method not supported, use POST']);
    }

    protected function getCartDetails(OrderInterface $cart): array
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
            $jsonCart['address_shipping_formatted'] = $this->addressFormatter->formatAddress($cart->getShippingAddress());
        } else {
            $jsonCart['address_shipping_formatted'] = '';
        }

        if ($cart->getInvoiceAddress() instanceof AddressInterface && $cart->getInvoiceAddress()->getCountry() instanceof CountryInterface) {
            $jsonCart['address_billing_formatted'] = $this->addressFormatter->formatAddress($cart->getInvoiceAddress());
        } else {
            $jsonCart['address_billing_formatted'] = '';
        }

        $totals = $this->getCartSummary($cart);

        $jsonCart['summary'] = $totals;

        return $jsonCart;
    }

    protected function getItemDetails(OrderInterface $cart): array
    {
        $items = [];

        foreach ($cart->getItems() as $item) {
            if ($item instanceof OrderItemInterface) {
                $items[] = $this->prepareCartItem($cart, $item);
            }
        }

        return $items;
    }

    protected function prepareCartItem(OrderInterface $cart, OrderItemInterface $item): array
    {
        return [
            'product' => $item->getProduct() ? $item->getProduct()->getId() : 0,
            'productName' => $item->getProduct() ? $item->getProduct()->getName() : '',
            'quantity' => $item->getQuantity(),
            'price' => $item->getItemPrice(),
            'total' => $item->getTotal(),
            'convertedPrice' => $item->getConvertedItemPrice(),
            'convertedTotal' => $item->getConvertedTotal(),
            'customItemPrice' => $item->getCustomItemPrice(),
            'customItemDiscount' => $item->getCustomItemDiscount(),
            'convertedCustomItemPrice' => $item->getConvertedCustomItemPrice(),
        ];
    }

    protected function getCartSummary(OrderInterface $cart): array
    {
        return [
            [
                'key' => 'subtotal',
                'value' => $cart->getSubtotal(true),
                'convertedValue' => $cart->getConvertedSubtotal(true),
            ],
            [
                'key' => 'subtotal_without_tax',
                'value' => $cart->getSubtotal(false),
                'convertedValue' => $cart->getConvertedSubtotal(false),
            ],
            [
                'key' => 'subtotal_tax',
                'value' => $cart->getSubtotalTax(),
                'convertedValue' => $cart->getConvertedSubtotalTax(),
            ],
            [
                'key' => 'discount',
                'value' => -1 * $cart->getDiscount(true),
                'convertedValue' => -1 * $cart->getConvertedDiscount(true),
            ],
            [
                'key' => 'discount_without_tax',
                'value' => -1 * $cart->getDiscount(false),
                'convertedValue' => -1 * $cart->getConvertedDiscount(false),
            ],
            [
                'key' => 'discount_tax',
                'value' => -1 * $cart->getDiscount(true) - $cart->getDiscount(false),
                'convertedValue' => -1 * $cart->getConvertedDiscount(true) - $cart->getConvertedDiscount(false),
            ],
            [
                'key' => 'payment_provider_fee',
                'value' => $cart->getSubtotal(true),
                'convertedValue' => -1 * $cart->getConvertedSubtotal(true),
            ],

            [
                'key' => 'total',
                'value' => $cart->getTotal(true),
                'convertedValue' => $cart->getConvertedTotal(true),
            ],
            [
                'key' => 'total_without_tax',
                'value' => $cart->getTotal(false),
                'convertedValue' => $cart->getConvertedTotal(false),
            ],
            [
                'key' => 'total_tax',
                'value' => $cart->getTotalTax(),
                'convertedValue' => $cart->getConvertedTotalTax(),
            ],
        ];
    }

    protected function getCurrency(CurrencyInterface $currency): array
    {
        return [
            'name' => $currency->getName(),
            'symbol' => $currency->getSymbol(),
            'isoCode' => $currency->getIsoCode(),
        ];
    }

    protected function getStore(StoreInterface $store): array
    {
        return [
            'id' => $store->getId(),
            'name' => $store->getName(),
        ];
    }

    protected function getDataForObject($data): array
    {
        if ($data instanceof Concrete) {
            $dataLoader = new DataLoader();

            return $dataLoader->getDataForObject($data);
        }

        return [];
    }

    public function setAddressFormatter(AddressFormatterInterface $addressFormatter): void
    {
        $this->addressFormatter = $addressFormatter;
    }

    protected function getPermission(): string
    {
        return 'coreshop_permission_order_create';
    }
}
