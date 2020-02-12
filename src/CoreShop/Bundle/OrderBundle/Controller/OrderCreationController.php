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
use CoreShop\Bundle\ResourceBundle\Controller\PimcoreController;
use CoreShop\Bundle\ResourceBundle\Form\Helper\ErrorSerializer;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManagerInterface;
use CoreShop\Component\Address\Formatter\AddressFormatterInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Customer\Repository\CustomerRepositoryInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\OrderSaleTransitions;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Pimcore\DataObject\DataLoader;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OrderCreationController extends PimcoreController
{
    /**
     * @var AddressFormatterInterface
     */
    protected $addressFormatter;

    /**
     * @var CurrencyConverterInterface
     */
    protected $currencyConverter;

    public function getCustomerDetailsAction(
        Request $request,
        CustomerRepositoryInterface $customerRepository
    ): Response {
        $this->isGrantedOr403();

        $customerId = $request->get('customerId');
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
        CartProcessorInterface $cartProcessor
    ): Response {
        $cart = $orderFactory->createNew();
        $form = $formFactory->createNamed('', CartCreationType::class, $cart, [
            'customer' => $request->get('customer'),
        ]);

        if ($request->getMethod() === 'POST') {
            $handledForm = $form->handleRequest($request);

            $cart = $handledForm->getData();

            $cartProcessor->process($cart);
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
        CartProcessorInterface $cartProcessor,
        ErrorSerializer $errorSerializer,
        StateMachineManagerInterface $manager
    ): Response {
        $this->isGrantedOr403();

        $type = $request->get('saleType', OrderSaleTransitions::TRANSITION_CART);

        $cart = $orderFactory->createNew();
        $form = $formFactory->createNamed('', CartCreationType::class, $cart, [
            'customer' => $request->get('customer'),
        ]);

        if ($request->getMethod() === 'POST') {
            $handledForm = $form->handleRequest($request);

            if (!$handledForm->isValid()) {
                return $this->viewHandler->handle(
                    [
                        'success' => false,
                        'message' => $errorSerializer->serializeErrorFromHandledForm($form),
                    ]
                );
            }

            $cart = $handledForm->getData();

            $workflow = $manager->get($cart, OrderSaleTransitions::IDENTIFIER);

            if (!$workflow->can($cart, $type)) {
                throw new HttpException(500);
            }

            $workflow->apply($cart, $type);

            $cartManager->persistCart($cart);

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

        foreach ($totals as &$totalEntry) {
            $price = $totalEntry['value'];
            $priceConverted = $this->currencyConverter->convert($price, $cart->getStore()->getCurrency()->getIsoCode(),
                $cart->getCurrency()->getIsoCode());
            $totalEntry['value'] = $priceConverted;
        }

        unset($totalEntry);

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
        $currentCurrency = $cart->getCurrency()->getIsoCode();
        $currency = $cart->getStore()->getCurrency()->getIsoCode();

        $price = $item->getItemPrice();
        $total = $item->getTotal();
        $basePrice = $this->currencyConverter->convert($price, $currentCurrency, $currency);
        $baseTotal = $this->currencyConverter->convert($total, $currentCurrency, $currency);

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

    protected function getCartSummary(OrderInterface $cart): array
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

    protected function getCurrency(CurrencyInterface $currency): array
    {
        return [
            'name' => $currency->getName(),
            'symbol' => $currency->getSymbol(),
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

    public function setCurrencyConverter(CurrencyConverterInterface $currencyConverter): void
    {
        $this->currencyConverter = $currencyConverter;
    }
}
