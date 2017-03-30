<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreShopLegacyBundle\Controller\Model;

use CoreShop\Bundle\CoreShopLegacyBundle\Controller\Admin;
use CoreShop\Bundle\CoreShopLegacyBundle\CoreShop;
use Pimcore\Model\Object;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class OrderController
 *
 * @Route("/order")
 */
class OrderController extends Admin\AdminController
{
    /**
     * @Route("/get-order-grid-configuration")
     */
    public function getOrderGridConfigurationAction(Request $request)
    {
        $defaultConfiguration = [
            [
                'text' => 'coreshop_orders_id',
                'type' => 'string',
                'dataIndex' => 'o_id',
                'filter' => [
                    'type' => 'number'
                ],
                'hideable' => false,
                'draggable' => false
            ],
            [
                'text' => 'coreshop_orders_orderNumber',
                'type' => 'string',
                'dataIndex' => 'orderNumber',
                'filter' => [
                    'type' => 'string'
                ]
            ],
            [
                'text' => 'name',
                'type' => 'string',
                'dataIndex' => 'customerName',
                'flex' => 1
            ],
            [
                'text' => 'email',
                'type' => 'string',
                'dataIndex' => 'customerEmail',
                'width' => 200
            ],
            [
                'text' => 'coreshop_orders_total',
                'type' => 'float',
                'dataIndex' => 'total',
                'renderAs' => 'currency',
                'filter' => [
                    'type' => 'number'
                ],
                'align' => 'right'
            ],
            [
                'text' => 'coreshop_discount',
                'type' => 'float',
                'dataIndex' => 'discount',
                'renderAs' => 'currency',
                'align' => 'right',
                'hidden' => true
            ],
            [
                'text' => 'coreshop_subtotal',
                'type' => 'float',
                'dataIndex' => 'subtotal',
                'renderAs' => 'currency',
                'align' => 'right',
                'hidden' => true
            ],
            [
                'text' => 'coreshop_shipping',
                'type' => 'float',
                'dataIndex' => 'shipping',
                'renderAs' => 'currency',
                'align' => 'right',
                'hidden' => true
            ],
            [
                'text' => 'coreshop_paymentFee',
                'type' => 'float',
                'dataIndex' => 'paymentFee',
                'renderAs' => 'currency',
                'align' => 'right',
                'hidden' => true
            ],
            [
                'text' => 'coreshop_total_tax',
                'type' => 'float',
                'dataIndex' => 'totalTax',
                'renderAs' => 'currency',
                'align' => 'right',
                'hidden' => true
            ],
            [
                'text' => 'coreshop_currency',
                'type' => 'string',
                'dataIndex' => 'currencyName',
                'align' => 'right',
                'hidden' => true
            ],
            [
                'text' => 'coreshop_orders_orderState',
                'type' => null,
                'dataIndex' => 'orderState',
                'renderAs' => 'orderState',
                'width' => 200
            ],
            [
                'text' => 'coreshop_orders_orderDate',
                'type' => 'date',
                'dataIndex' => 'orderDate',
                'filter' => [
                    'type' => 'date'
                ],
                'width' => 150
            ]
        ];

        $addressClassId = \CoreShop\Bundle\CoreShopLegacyBundle\Model\User\Address::classId();

        $addressClassDefinition = \Pimcore\Model\Object\ClassDefinition::getById($addressClassId);
        $addressFields = [];

        if ($addressClassDefinition instanceof \Pimcore\Model\Object\ClassDefinition) {
            $invalidFields = array('extra');

            foreach($addressClassDefinition->getFieldDefinitions() as $fieldDefinition) {
                if(in_array($fieldDefinition->getName(), $invalidFields)) {
                    continue;
                }

                $niceName = ucwords(str_replace('_', ' ', $fieldDefinition->getName()));

                $addressFields[] = [
                    'fieldName' => $niceName,
                    'type' => 'string',
                    'dataIndex' => $fieldDefinition->getName(),
                    'width' => 150,
                    'hidden' => true
                ];
            }

            $addressFields[] = [
                'fieldName' => 'All',
                'type' => 'string',
                'dataIndex' => 'All',
                'width' => 150,
                'hidden' => true
            ];
        }

        foreach (['shipping', 'billing'] as $type) {
            foreach ($addressFields as $fieldElement) {
                $name = $fieldElement['fieldName'];
                $dataIndex = $fieldElement['dataIndex'];

                $fieldElement['text'] = 'coreshop_address_'.$type.'|[' . $name . ']';
                $fieldElement['dataIndex'] = 'address' . ucfirst($type) . ucfirst($dataIndex);

                $defaultConfiguration[] = $fieldElement;
            }
        }

        if (\CoreShop\Bundle\CoreShopLegacyBundle\Model\Configuration::multiShopEnabled()) {
            array_splice($defaultConfiguration, 1, 0, [[
                'text' => 'coreshop_shop',
                'type' => 'integer',
                'dataIndex' => 'shop',
                'renderAs' => 'shop',
                'filter' => [
                    'type' => 'number'
                ]
            ]]);
        }

        return $this->json(["success" => true, "columns" => $defaultConfiguration]);
    }

    /**
     * @Route("/get-orders")
     */
    public function getOrdersAction(Request $request)
    {
        $list = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order::getList();
        $list->setLimit($request->get('limit', 30));
        $list->setOffset($request->get('page', 1) - 1);

        if ($request->get('filter', null)) {
            $conditionFilters = [];
            $conditionFilters[] = \Pimcore\Model\Object\Service::getFilterCondition($request->get('filter'), \Pimcore\Model\Object\ClassDefinition::getById(\CoreShop\Bundle\CoreShopLegacyBundle\Model\Order::classId()));
            if (count($conditionFilters) > 0 && $conditionFilters[0] !== '(())') {
                $list->setCondition(implode(' AND ', $conditionFilters));
            }
        }

        $sortingSettings = \Pimcore\Admin\Helper\QueryParams::extractSortingSettings($request->request->all());

        $order = 'DESC';
        $orderKey = 'orderDate';

        if ($sortingSettings['order']) {
            $order = $sortingSettings['order'];
        }
        if (strlen($sortingSettings['orderKey']) > 0) {
            $orderKey = $sortingSettings['orderKey'];
        }

        $list->setOrder($order);
        $list->setOrderKey($orderKey);

        $orders = $list->load();
        $jsonOrders = [];

        foreach ($orders as $order) {
            $jsonOrders[] = $this->prepareOrder($order);
        }

        return $this->json(['success' => true, 'data' => $jsonOrders, 'count' => count($jsonOrders), 'total' => $list->getTotalCount()]);
    }

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order $order
     * @return array
     */
    protected function prepareOrder(\CoreShop\Bundle\CoreShopLegacyBundle\Model\Order $order)
    {
        $date = "";

        if ($order->getOrderDate() instanceof \Pimcore\Date) {
            $date = intval($order->getOrderDate()->get(\Zend_Date::TIMESTAMP));
        } elseif ($order->getOrderDate() instanceof \Carbon\Carbon) {
            $date = intval($order->getOrderDate()->getTimestamp());
        }

        $element = [
            'o_id' => $order->getId(),
            'orderState' => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\State::getOrderCurrentState($order),
            'orderDate' => $date,
            'orderNumber' => $order->getOrderNumber(),
            'lang' => $order->getLang(),
            'carrier' => $order->getCarrier() instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier ? $order->getCarrier()->getId() : null,
            'discount' => $order->getDiscount(),
            'subtotal' => $order->getSubtotal(),
            'shipping' => $order->getShipping(),
            'paymentFee' => $order->getPaymentFee(),
            'totalTax' => $order->getTotalTax(),
            'total' => $order->getTotal(),
            'currency' => $this->getCurrency($order->getCurrency() ? $order->getCurrency() : \CoreShop\Bundle\CoreShopLegacyBundle\CoreShop::getTools()->getCurrency()),
            'currencyName' => $order->getCurrency() instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Currency ? $order->getCurrency()->getName() : '',
            'shop' => $order->getShop() instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Shop ? $order->getShop()->getId() : null,
            'customerName' => $order->getCustomer() instanceof CoreShop\Bundle\CoreShopLegacyBundle\Model\User ? $order->getCustomer()->getFirstname() . ' ' . $order->getCustomer()->getLastname() : '',
            'customerEmail' => $order->getCustomer() instanceof CoreShop\Bundle\CoreShopLegacyBundle\Model\User ? $order->getCustomer()->getEmail() : ''
        ];

        $element = array_merge($element, $this->prepareAddress($order->getShippingAddress(), 'shipping'), $this->prepareAddress($order->getBillingAddress(), 'billing'));

        return $element;
    }

    /**
     * @param $address
     * @param $type
     * @return array
     */
    protected function prepareAddress($address, $type) {
        $prefix = "address" . ucfirst($type);
        $values = [];
        $fullAddress = [];
        $classDefinition = Object\ClassDefinition::getById(\CoreShop\Bundle\CoreShopLegacyBundle\Model\User\Address::classId());

        foreach($classDefinition->getFieldDefinitions() as $fieldDefinition) {
            $value = "";

            if ($address instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\User\Address) {
                $value = $address->getValueForFieldName($fieldDefinition->getName());

                if($value instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel) {
                    $value = $value->getName();
                }

                $fullAddress[] = $value;
            }

            $values[$prefix . ucfirst($fieldDefinition->getName())] = $value;
        }

        if ($address instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\User\Address && $address->getCountry() instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Country) {
            $values[$prefix . "All"] = $address->getCountry()->formatAddress($address, false);
        }

        return $values;
    }

    /**
     * @Route("/get-payment-providers")
     */
    public function getPaymentProvidersAction(Request $request)
    {
        $providers = CoreShop::getPaymentProviders();
        $result = [];

        foreach ($providers as $provider) {
            if ($provider instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Plugin\Payment) {
                $result[] = [
                    'name' => $provider->getName(),
                    'id' => $provider->getIdentifier(),
                ];
            }
        }

        return $this->json(['success' => true, 'data' => $result]);
    }

    /**
     * @Route("/add-payment")
     */
    public function addPaymentAction(Request $request)
    {
        //@TODO: Add translations for messages

        $orderId = $request->get('o_id');
        $order = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order::getById($orderId);
        $amount = doubleval($request->get('amount', 0));
        $transactionId = $request->get('transactionNumber');
        $paymentProviderName = $request->get('paymentProvider');

        if (!$order instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order) {
            return $this->json(['success' => false, 'message' => 'Order with ID "'.$orderId.'" not found']);
        }

        $paymentProvider = \CoreShop\Bundle\CoreShopLegacyBundle\CoreShop::getPaymentProvider($paymentProviderName);

        if ($paymentProvider instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Plugin\Payment) {
            $payedTotal = $order->getPayedTotal();

            $payedTotal += $amount;

            if ($payedTotal > $order->getTotal()) {
                return $this->json(['success' => false, 'message' => 'Payed Amount is greater than order amount']);
            } else {
                $order->createPayment($paymentProvider, $amount, true, $transactionId);
                return $this->json(['success' => true, 'payments' => $this->getPayments($order), 'totalPayed' => $order->getPayedTotal()]);
            }
        } else {
            return $this->json(['success' => false, 'message' => "Payment Provider '$paymentProviderName' not found"]);
        }
    }

    /**
     * @Route("/send-message")
     */
    public function sendMessageAction(Request $request)
    {
        $orderId = $request->get('o_id');
        $order = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order::getById($orderId);
        $messageText = $request->get('message', '');

        if (!$order instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order) {
            return $this->json(['success' => false, 'message' => "Order with ID '$orderId' not found"]);
        }

        if (strlen($messageText) <= 0) {
            return $this->json(['success' => false, 'message' => 'No Message text set']);
        }

        $salesContact = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Contact::getById(\CoreShop\Bundle\CoreShopLegacyBundle\Model\Configuration::get('SYSTEM.MESSAGING.CONTACT.SALES'));
        $thread = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Thread::searchThread($order->getCustomer()->getEmail(), $salesContact->getId(), $order->getShop()->getId(), $orderId);

        if (!$thread instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Thread) {
            $thread = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Thread::create();
            $thread->setLanguage($order->getLang());
            $thread->setStatusId(\CoreShop\Bundle\CoreShopLegacyBundle\Model\Configuration::get('SYSTEM.MESSAGING.THREAD.STATE.NEW'));
            $thread->setEmail($order->getCustomer()->getEmail());
            $thread->setUser($order->getCustomer());
            $thread->setContact($salesContact);
            $thread->setShopId($order->getShop()->getId());
            $thread->setToken(uniqid());
            $thread->setOrder($order);
            $thread->save();
        }

        if ($thread instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Thread) {
            $message = $thread->createMessage($messageText);

            $message->sendNotification('customer-reply', $thread->getEmail());
        }

        return $this->json(['success' => true]);
    }

    /**
     * @Route("/change-order-item")
     */
    public function changeOrderItemAction(Request $request)
    {
        $orderId = $request->get('id');
        $orderItemId = $request->get("orderItemId");
        $amount = $request->get("amount");
        $price = $request->get("price");

        $order = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order::getById($orderId);
        $orderItem = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Item::getById($orderItemId);

        if (!$order instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order) {
            return $this->json(['success' => false, 'message' => "Order with ID '$orderId' not found"]);
        }

        if (!$orderItem instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Item) {
            return $this->json(['success' => false, 'message' => "OrderItem with ID '$orderItemId' not found"]);
        }

        $order->updateOrderItem($orderItem, $amount, $price);

        return $this->json(['success' => true, "summary" => $this->getSummary($order), "details" => $this->getDetails($order), "total" => $order->getTotal()]);
    }

    /**
     * @Route("/detail")
     */
    public function detailAction(Request $request)
    {
        $orderId = $request->get('id');
        $order = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order::getById($orderId);

        if (!$order instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order) {
            return $this->json(['success' => false, 'message' => "Order with ID '$orderId' not found"]);
        }

        $jsonOrder = $this->getDataForObject($order);

        if ($jsonOrder['items'] === null) {
            $jsonOrder['items'] = [];
        }

        $jsonOrder['o_id'] = $order->getId();
        $jsonOrder['customer'] = $order->getCustomer() instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Base ? $this->getDataForObject($order->getCustomer()) : null;
        $jsonOrder['statesHistory'] = $this->getStatesHistory($order);
        $jsonOrder['invoice'] = $order->getProperty('invoice');
        $jsonOrder['invoices'] = $this->getInvoices($order);
        $jsonOrder['shipments'] = $this->getShipments($order);
        $jsonOrder['mailCorrespondence'] = $this->getMailCorrespondence($order);
        $jsonOrder['payments'] = $this->getPayments($order);
        $jsonOrder['editable'] = count($order->getInvoices()) > 0 ? false : true;
        $jsonOrder['totalPayed'] = $order->getPayedTotal();
        $jsonOrder['details'] = $this->getDetails($order);
        $jsonOrder['summary'] = $this->getSummary($order);
        $jsonOrder['currency'] = $this->getCurrency($order->getCurrency() ? $order->getCurrency() : \CoreShop\Bundle\CoreShopLegacyBundle\CoreShop::getTools()->getCurrency());
        $jsonOrder['shop'] = $order->getShop() instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Shop ? $order->getShop()->getObjectVars() : null;
        $jsonOrder['visitor'] = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Visitor::getById($order->getVisitorId());
        $jsonOrder['invoiceCreationAllowed'] = !$order->isFullyInvoiced() && $order->hasPayments();
        $jsonOrder['shipmentCreationAllowed'] = !$order->isFullyShipped() && $order->hasPayments();

        $jsonOrder['address'] = [
            'shipping' => $this->getDataForObject($order->getShippingAddress()),
            'billing' => $this->getDataForObject($order->getBillingAddress()),
        ];

        if($order->getShippingAddress() instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\User\Address && $order->getShippingAddress()->getCountry() instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Country) {
            $jsonOrder['address']['shipping']['formatted'] = $order->getShippingAddress()->getCountry()->formatAddress($order->getShippingAddress());
        }
        else {
            $jsonOrder['address']['shipping']['formatted'] = '';
        }

        if($order->getBillingAddress() instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\User\Address && $order->getBillingAddress()->getCountry() instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Country) {
            $jsonOrder['address']['billing']['formatted'] = $order->getBillingAddress()->getCountry()->formatAddress($order->getBillingAddress());
        }
        else {
            $jsonOrder['address']['billing']['formatted'] = '';
        }

        $jsonOrder['shippingPayment'] = [
            'carrier' => $order->getCarrier() instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier ? $order->getCarrier()->getName() : null,
            'weight' => $order->getTotalWeight(),
            'cost' => $order->getShipping(),
            'payment' => $order->getPaymentProvider(),
            'paymentToken' => $order->getPaymentProviderToken(),
            'paymentDescription' => $order->getPaymentProviderDescription()
        ];

        $jsonOrder['priceRule'] = false;

        if ($order->getPriceRuleFieldCollection() instanceof Object\Fieldcollection) {
            $rules = [];

            foreach ($order->getPriceRuleFieldCollection()->getItems() as $ruleItem) {
                if ($ruleItem instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\PriceRule\Item) {
                    $rule = $ruleItem->getPriceRule();

                    if ($rule instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart\PriceRule) {
                        $rules[] = [
                            'id' => $rule->getId(),
                            'name' => $rule->getName(),
                            'code' => $ruleItem->getVoucherCode(),
                            'discount' => $ruleItem->getDiscount()
                        ];
                    }
                }
            }

            $jsonOrder['priceRule'] = $rules;
        }

        $jsonOrder['threads'] = [];
        $jsonOrder['unreadMessages'] = 0;

        $threads = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Thread::getList();
        $threads->setCondition("orderId = ?", [$order->getId()]);
        $threads->load();

        foreach ($threads as $thread) {
            if ($thread instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Thread) {
                $threadResult = $thread->getObjectVars();

                $messageList = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Message::getList();
                $messageList->setCondition("threadId = ? AND `read` = '0'", [$thread->getId()]);
                $messageList->load();

                $threadResult['unread'] = count($messageList->getData());
                $jsonOrder['unreadMessages'] += $threadResult['unread'];
                $jsonOrder['threads'][] = $threadResult;
            }
        }


        return $this->json(["success" => true, "order" => $jsonOrder]);
    }

    /**
     * @Route("/get-address-fields")
     */
    public function getAddressFieldsAction(Request $request)
    {
        $orderId = $request->get('id');
        $order = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order::getById($orderId);
        $addressType = $request->get('type');

        if (!$order instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order) {
            return $this->json(['success' => false, 'message' => "Order with ID '$orderId' not found"]);
        }

        $addressClassId = \CoreShop\Bundle\CoreShopLegacyBundle\Model\User\Address::classId();

        $fieldCollection = \Pimcore\Model\Object\ClassDefinition::getById($addressClassId);

        if ($fieldCollection instanceof \Pimcore\Model\Object\ClassDefinition) {
            return $this->json([
                'success' => true,
                'data' => $addressType == 'shipping' ? $this->getDataForObject($order->getShippingAddress()) : $this->getDataForObject($order->getBillingAddress()),
                'layout' => $fieldCollection->getLayoutDefinitions()
            ]);
        }

        return $this->json(['success' => true]);
    }

    /**
     * @Route("/change-address")
     */
    public function changeAddressAction(Request $request)
    {
        $orderId = $request->get('id');
        $order = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order::getById($orderId);
        $addressType = $request->get('type');
        $data = $request->request->all();

        if (!$order instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order) {
            return $this->json(['success' => false, 'message' => "Order with ID '$orderId' not found"]);
        }

        $address = $addressType == 'shipping' ? $order->getShippingAddress() : $order->getBillingAddress();

        unset($data['action']);
        unset($data['module']);
        unset($data['controller']);
        unset($data['type']);
        unset($data['_dc']);
        unset($data['id']);

        if ($address instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\User\Address) {
            $address->setValues($data);
            $address->save();

            if ($order->getProperty('invoice') instanceof \Pimcore\Model\Asset) {
                foreach ($order->getInvoices() as $invoice) {
                    $invoice->generate();
                }
            }

            return $this->json(['success' => true]);
        }

        return $this->json(['success' => false]);
    }

    /**
     * @Route("/get-customer-details")
     */
    public function getCustomerDetailsAction(Request $request)
    {
        $customerId = $request->get("customerId");
        $user = \CoreShop\Bundle\CoreShopLegacyBundle\Model\User::getById($customerId);

        if (!$user instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\User) {
            return $this->json(['success' => false, 'message' => "Customer with ID '$customerId' not found"]);
        }

        return $this->json(['success' => true, 'customer' => $this->getDataForObject($user)]);
    }

    /**
     * @Route("/get-customer-carts")
     */
    public function getCustomerCartsAction(Request $request)
    {
        $customerId = $request->get("customerId");
        $user = \CoreShop\Bundle\CoreShopLegacyBundle\Model\User::getById($customerId);

        if (!$user instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\User) {
            return $this->json(['success' => false, 'message' => "Customer with ID '$customerId' not found"]);
        }

        $manager = new \CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart\Manager();
        $carts = $manager->getCarts($user);
        $result = [];

        foreach ($carts as $cart) {
            $productIds = [];

            foreach ($cart->getItems() as $item) {
                $productIds[] = [
                    'id' => $item->getProduct()->getId(),
                    'amount' => $item->getAmount()
                ];
            }

            $result[] = [
                "id" => $cart->getId(),
                "date" => $cart->getCreationDate(),
                "total" => $cart->getTotal(true),
                "name" => $cart->getName(),
                "currency" => $this->getCurrency(CoreShop::getTools()->getBaseCurrency()),
                "productIds" => $productIds
            ];
        }

        return $this->json(['success' => true, 'carts' => $result]);
    }

    /**
     * @Route("/get-customer-orders")
     */
    public function getCustomerOrdersAction(Request $request)
    {
        $customerId = $request->get("customerId");
        $user = \CoreShop\Bundle\CoreShopLegacyBundle\Model\User::getById($customerId);

        if (!$user instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\User) {
            return $this->json(['success' => false, 'message' => "Customer with ID '$customerId' not found"]);
        }

        $orders = $user->getOrders();
        $result = [];

        foreach ($orders as $order) {
            if ($order instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order) {
                $productIds = [];

                foreach ($order->getItems() as $item) {
                    if ($item->getProduct() instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Product) {
                        $productIds[] = [
                            'id' => $item->getProduct()->getId(),
                            'amount' => $item->getAmount()
                        ];
                    }
                }

                $result[] = [
                    "id" => $order->getId(),
                    "date" => $order->getOrderDate() instanceof \Carbon\Carbon ? $order->getOrderDate()->getTimestamp() : ($order->getOrderDate() instanceof \Pimcore\Date ? $order->getOrderDate()->getTimestamp() : 0),
                    "total" => $order->getTotal(),
                    "currency" => $this->getCurrency($order->getCurrency()),
                    "productIds" => $productIds
                ];
            }
        }

        return $this->json(['success' => true, 'orders' => $result]);
    }

    /**
     * @Route("/get-product-details")
     */
    public function getProductDetailsAction(Request $request)
    {
        $productIds = \Zend_Json::decode($request->get("products"));
        $currency = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Currency::getById($request->get("currency"));

        $result = [];

        foreach ($productIds as $productObject) {
            $productId = $productObject['id'];

            $product = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Product::getById($productId);

            if ($product instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Product) {
                $productFlat = $this->getDataForObject($product);

                $productFlat['amount'] = $productObject['amount'];

                $productFlat['price'] = \CoreShop\Bundle\CoreShopLegacyBundle\CoreShop::getTools()->convertToCurrency($product->getPrice(true, false), $currency);
                $result[] = $productFlat;
            }
        }

        return $this->json(['success' => true, 'products' => $result]);
    }

    /**
     * @Route("/get-carriers-details")
     */
    public function getCarriersDetailsAction(Request $request)
    {
        $productIds = \Zend_Json::decode($request->get("products"));
        $customerId = $request->get("customerId");
        $shippingAddressId = $request->get("shippingAddress");
        $billingAddressId = $request->get("billingAddress");

        $currency = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Currency::getById($request->get("currency"));

        $user = \CoreShop\Bundle\CoreShopLegacyBundle\Model\User::getById($customerId);
        $shippingAddress = \CoreShop\Bundle\CoreShopLegacyBundle\Model\User\Address::getById($shippingAddressId);
        $billingAddress = \CoreShop\Bundle\CoreShopLegacyBundle\Model\User\Address::getById($billingAddressId);

        $result = [];

        if (!$user instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\User) {
            return $this->json(['success' => false, 'message' => "Customer with ID '$customerId' not found"]);
        }

        if (!$shippingAddress instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\User\Address) {
            return $this->json(['success' => false, 'message' => "Address with ID '$shippingAddressId' not found"]);
        }

        if (!$billingAddress instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\User\Address) {
            return $this->json(['success' => false, 'message' => "Address with ID '$billingAddressId' not found"]);
        }

        $cart = $this->createTempCart($user, $shippingAddress, $billingAddress, $currency, $productIds);

        $carriers = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier::getCarriersForCart($cart, $cart->getShippingAddress());

        foreach ($carriers as $carrier) {
            $price = $carrier->getDeliveryPrice($cart, true, $cart->getShippingAddress());

            $result[] = [
                "id" => $carrier->getId(),
                "name" => $carrier->getName(),
                "price" => \CoreShop\Bundle\CoreShopLegacyBundle\CoreShop::getTools()->convertToCurrency($price, $currency)
            ];
        }

        $cart->delete();

        return $this->json(['success' => true, 'carriers' => $result]);
    }

    /**
     * @Route("/get-order-total")
     */
    public function getOrderTotalAction(Request $request)
    {
        $productIds = \Zend_Json::decode($request->get('products'));
        $customerId = $request->get('customerId');
        $shippingAddressId = $request->get('shippingAddress');
        $billingAddressId = $request->get('billingAddress');
        $carrierId = $request->get('carrier');
        $freeShipping = $request->get('freeShipping');

        $currency = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Currency::getById($request->get('currency'));

        $user = \CoreShop\Bundle\CoreShopLegacyBundle\Model\User::getById($customerId);
        $shippingAddress = \CoreShop\Bundle\CoreShopLegacyBundle\Model\User\Address::getById($shippingAddressId);
        $billingAddress = \CoreShop\Bundle\CoreShopLegacyBundle\Model\User\Address::getById($billingAddressId);
        $carrier = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier::getById($carrierId);

        if (!$user instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\User) {
            return $this->json(['success' => false, 'message' => "Customer with ID '$customerId' not found"]);
        }

        if (!$shippingAddress instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\User\Address) {
            return $this->json(['success' => false, 'message' => "Address with ID '$shippingAddressId' not found"]);
        }

        if (!$billingAddress instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\User\Address) {
            return $this->json(['success' => false, 'message' => "Address with ID '$billingAddressId' not found"]);
        }

        if (!$carrier instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier) {
            return $this->json(['success' => false, 'message' => "Carrier with ID '$carrierId' not found"]);
        }

        $cart = $this->createTempCart($user, $shippingAddress, $billingAddress, $currency, $productIds);
        $cart->setCarrier($carrier);
        $cart->setFreeShipping($freeShipping);
        $cart->save();

        $values = [
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
                'value' =>$cart->getSubtotal(false)
            ],
            [
                'key' => 'shipping_without_tax',
                'value' =>$cart->getShipping(false)
            ],
            [
                'key' => 'shipping_tax',
                'value' => $cart->getShippingTax()
            ],
            [
                'key' => 'shipping',
                'value' => $cart->getShipping(true)
            ],
            [
                'key' => 'discount_without_tax',
                'value' => -1 * $cart->getDiscount(false)
            ],
            [
                'key' => 'discount_tax',
                'value' => -1 * $cart->getDiscountTax()
            ],
            [
                'key' => 'discount',
                'value' => -1 * $cart->getDiscount(true)
            ],
            [
                'key' => 'total_without_tax',
                'value' =>$cart->getTotal(false)
            ],
            [
                'key' => 'total_tax',
                'value' =>$cart->getTotalTax()
            ],
            [
                'key' => 'total',
                'value' => $cart->getTotal(true)
            ]
        ];

        $cart->delete();

        return $this->json(['success' => true, 'summary' => $values]);
    }

    /**
     * @Route("/create-order")
     */
    public function createOrderAction(Request $request)
    {
        $productIds = \Zend_Json::decode($request->get('products'));
        $customerId = $request->get('customerId');
        $shippingAddressId = $request->get('shippingAddress');
        $billingAddressId = $request->get('billingAddress');
        $carrierId = $request->get('carrier');
        $freeShipping = $request->get('freeShipping');
        $paymentModuleName = $request->get('paymentProvider');
        $shopId = $request->get('shop');

        $language = $request->get('language');
        $currency = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Currency::getById($request->get('currency'));

        $user = \CoreShop\Bundle\CoreShopLegacyBundle\Model\User::getById($customerId);
        $shippingAddress = \CoreShop\Bundle\CoreShopLegacyBundle\Model\User\Address::getById($shippingAddressId);
        $billingAddress = \CoreShop\Bundle\CoreShopLegacyBundle\Model\User\Address::getById($billingAddressId);
        $carrier = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier::getById($carrierId);
        $paymentModule = \CoreShop\Bundle\CoreShopLegacyBundle\CoreShop::getPaymentProvider($paymentModuleName);
        $shop = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Shop::getById($shopId);

        if (!$user instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\User) {
            return $this->json(['success' => false, 'message' => "Customer with ID '$customerId' not found"]);
        }

        if (!$shippingAddress instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\User\Address) {
            return $this->json(['success' => false, 'message' => "Address with ID '$shippingAddressId' not found"]);
        }

        if (!$billingAddress instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\User\Address) {
            return $this->json(['success' => false, 'message' => "Address with ID '$billingAddressId' not found"]);
        }

        if (!$carrier instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier) {
            return $this->json(['success' => false, 'message' => "Carrier with ID '$carrierId' not found"]);
        }

        if (!$paymentModule instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Plugin\Payment) {
            return $this->json(['success' => false, 'message' => "Payment Module with ID '$paymentModuleName' not found"]);
        }

        if (!$shop instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Shop) {
            return $this->json(['success' => false, 'message' => "Shop with ID '$shopId' not found"]);
        }

        $cart = $this->createTempCart($user, $shippingAddress, $billingAddress, $currency, $productIds);
        $cart->setCarrier($carrier);
        $cart->setShop($shop);
        $cart->setFreeShipping($freeShipping);
        $cart->save();

        $order = $cart->createOrder($paymentModule, $language);

        $cart->delete();

        return $this->json(['success' => true, 'orderId' => $order->getId()]);
    }

    /**
     * @param $user
     * @param $shippingAddress
     * @param $billingAddress
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\Currency $currency
     * @param $productIds
     * @return \CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart
     */
    protected function createTempCart($user, $shippingAddress, $billingAddress, $currency, $productIds)
    {
        $cart = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart::create();
        $cart->setParent(\Pimcore\Model\Object\Service::createFolderByPath("/coreshop/tmp"));
        $cart->setKey(uniqid());
        $cart->setShippingAddress($shippingAddress);
        $cart->setBillingAddress($billingAddress);
        $cart->setCurrency($currency);
        $cart->setUser($user);
        $cart->setCurrency($currency);
        $cart->save();

        foreach ($productIds as $productObject) {
            $productId = $productObject['id'];

            $product = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Product::getById($productId);

            if ($product instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Product) {
                $cart->addItem($product, $productObject['amount']);
            }
        }

        return $cart;
    }

    /**
     * @param Object\Concrete $data
     * @return array
     */
    private function getDataForObject(Object\Concrete $data)
    {
        $objectData = [];
        Object\Service::loadAllObjectFields($data);

        foreach ($data->getClass()->getFieldDefinitions() as $key => $def) {
            $getter = "get" . ucfirst($key);
            $fieldData = $data->$getter();

            if ($def instanceof Object\ClassDefinition\Data\Href) {
                if ($fieldData instanceof Object\Concrete) {
                    $objectData[$key] = $this->getDataForObject($fieldData);
                }
            } elseif ($def instanceof Object\ClassDefinition\Data\Multihref) {
                $objectData[$key] = [];

                foreach ($fieldData as $object) {
                    if ($object instanceof Object\Concrete) {
                        $objectData[$key][] = $this->getDataForObject($object);
                    }
                }
            } elseif ($def instanceof Object\ClassDefinition\Data) {
                $value = $def->getDataForEditmode($fieldData, $data, false);

                $objectData[$key] = $value;
            } else {
                $objectData[$key] = null;
            }
        }

        $objectData['o_id'] = $data->getId();
        $objectData['o_creationDate'] = $data->getCreationDate();
        $objectData['o_modificationDate'] = $data->getModificationDate();

        return $objectData;
    }

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order $order
     * @return array
     */
    protected function getStatesHistory(\CoreShop\Bundle\CoreShopLegacyBundle\Model\Order $order)
    {
        //Get History
        $history = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\State::getOrderStateHistory($order);

        // create timeline
        $statesHistory = [];

        $date = new \Pimcore\Date();

        if (is_array($history)) {
            foreach ($history as $note) {
                $user = \Pimcore\Model\User::getById($note->getUser());
                $avatar = $user ? sprintf('/admin/user/get-image?id=%d', $user->getId()) : null;

                $statesHistory[] = [
                    'icon' => 'coreshop_icon_orderstates',
                    'type' => $note->getType(),
                    'date' => $date->setTimestamp($note->getDate())->get(\Pimcore\Date::DATETIME_MEDIUM),
                    'avatar' => $avatar,
                    'user' => $user ? $user->getName() : null,
                    'description' => $note->getDescription(),
                    'title' => $note->getTitle(),
                    'data' => $note->getData()
                ];
            }
        }

        return $statesHistory;
    }

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order $order
     * @return array
     * @throws \CoreShop\Bundle\CoreShopLegacyBundle\Exception\UnsupportedException
     */
    protected function getPayments(\CoreShop\Bundle\CoreShopLegacyBundle\Model\Order $order)
    {
        $payments = $order->getPayments();
        $return = [];

        foreach ($payments as $payment) {
            $noteList = new \Pimcore\Model\Element\Note\Listing();
            $noteList->addConditionParam('type = ?', \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Payment::NOTE_TRANSACTION);
            $noteList->addConditionParam('cid = ?', $payment->getId());
            $noteList->setOrderKey('date');
            $noteList->setOrder('desc');

            $return[] = [
                'id' => $payment->getId(),
                'datePayment' => $payment->getDatePayment() ? $payment->getDatePayment()->getTimestamp() : '',
                'provider' => $payment->getProvider(),
                'transactionIdentifier' => $payment->getTransactionIdentifier(),
                'transactionNotes' => $noteList->load(),
                'amount' => $payment->getAmount()
            ];
        }

        return $return;
    }

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order $order
     * @return array
     * @throws \CoreShop\Bundle\CoreShopLegacyBundle\Exception\UnsupportedException
     */
    protected function getDetails(\CoreShop\Bundle\CoreShopLegacyBundle\Model\Order $order)
    {
        $details = $order->getItems();
        $items = [];

        foreach ($details as $detail) {
            $items[] = [
                'o_id' => $detail->getId(),
                'product' => $detail->getProduct() instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Product ? $detail->getProduct()->getId() : null,
                'product_name' => $detail->getProductName(),
                'product_image' => ($detail->getProductImage() instanceof \Pimcore\Model\Asset\Image) ? $detail->getProductImage()->getPath() : null,
                'wholesale_price' => $detail->getWholesalePrice(),
                'price_without_tax' => $detail->getPriceWithoutTax(),
                'price' => $detail->getPrice(),
                'amount' => $detail->getAmount(),
                'total' => $detail->getTotal(),
                'total_tax' => $detail->getTotalTax()
            ];
        }

        return $items;
    }

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order $order
     * @return array
     * @throws \CoreShop\Bundle\CoreShopLegacyBundle\Exception\UnsupportedException
     */
    protected function getSummary(\CoreShop\Bundle\CoreShopLegacyBundle\Model\Order $order)
    {
        $summary = [];

        if ($order->getDiscount() > 0) {
            $summary[] = [
                'key' => 'discount',
                'value' => $order->getDiscount()
            ];
        }

        if ($order->getShipping() > 0) {
            $summary[] = [
                'key' => 'shipping',
                'value' => $order->getShipping()
            ];

            $summary[] = [
                'key' => 'shipping_tax',
                'value' => $order->getShippingTax()
            ];
        }

        if ($order->getPaymentFee() > 0) {
            $summary[] = [
                'key' => 'payment',
                'value' => $order->getPaymentFee()
            ];
        }

        $taxes = $order->getTaxes();

        if (is_array($taxes)) {
            foreach ($taxes as $tax) {
                if ($tax instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Tax) {
                    $summary[] = [
                        'key' => 'tax_' . $tax->getName(),
                        'text' => sprintf($this->view->translateAdmin('Tax (%s - %s)'), $tax->getName(), \CoreShop\Bundle\CoreShopLegacyBundle\CoreShop::getTools()->formatTax($tax->getRate())),
                        'value' => $tax->getAmount()
                    ];
                }
            }
        }

        $summary[] = [
            'key' => 'total_tax',
            'value' => $order->getTotalTax()
        ];
        $summary[] = [
            'key' => 'total',
            'value' => $order->getTotal()
        ];

        return $summary;
    }

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order $order
     * @return array
     */
    protected function getInvoices($order)
    {
        $invoices = $order->getInvoices();
        $invoiceArray = [];

        foreach ($invoices as $invoice) {
            $invoiceArray[] = $this->getDataForObject($invoice);
        }

        return $invoiceArray;
    }

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order $order
     * @return array
     */
    protected function getShipments($order)
    {
        $shipments = $order->getShipments();
        $shipmentArray = [];

        foreach ($shipments as $shipment) {
            $shipmentArray[] = $this->getDataForObject($shipment);
        }

        return $shipmentArray;
    }

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order $order
     *
     * @return array
     */
    protected function getMailCorrespondence(\CoreShop\Bundle\CoreShopLegacyBundle\Model\Order $order)
    {
        $list = [];

        $noteList = new \Pimcore\Model\Element\Note\Listing();
        $noteList->addConditionParam('type = ?', \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order::NOTE_EMAIL);
        $noteList->addConditionParam('cid = ?', $order->getId());
        $noteList->setOrderKey('date');
        $noteList->setOrder('desc');

        $objects = $noteList->load();

        foreach ($objects as $note) {
            $noteElement = [
                'date' => $note->date,
                'description' => $note->description
            ];

            foreach ($note->data as $key => $noteData) {
                $noteElement[$key] = $noteData['data'];
            }

            if (array_key_exists('messageId', $noteElement)) {
                $message = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Message::getById($noteElement['messageId']);

                if ($message instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Message) {
                    $noteElement['read'] = $message->getRead();
                }
            }

            $list[] = $noteElement;
        }

        return $list;
    }

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\Currency $currency
     * @return array
     */
    protected function getCurrency(\CoreShop\Bundle\CoreShopLegacyBundle\Model\Currency $currency)
    {
        return [
            'name' => $currency->getName(),
            'symbol' => $currency->getSymbol()
        ];
    }
}
