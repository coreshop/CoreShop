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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

use CoreShop\Controller\Action\Admin;
use Pimcore\Model\Object;

/**
 * Class CoreShop_Admin_OrderController
 */
class CoreShop_Admin_OrderController extends Admin
{
    public function getOrdersAction()
    {
        $list = \CoreShop\Model\Order::getList();
        $list->setLimit($this->getParam('limit', 30));
        $list->setOffset($this->getParam('page', 1) - 1);

        if ($this->getParam('filter', null)) {
            $conditionFilters[] = \Pimcore\Model\Object\Service::getFilterCondition($this->getParam('filter'), \Pimcore\Model\Object\ClassDefinition::getById(\CoreShop\Model\Order::classId()));
            if (count($conditionFilters) > 0 && $conditionFilters[0] !== '(())') {
                $list->setCondition(implode(' AND ', $conditionFilters));
            }
        }

        $sortingSettings = \Pimcore\Admin\Helper\QueryParams::extractSortingSettings($this->getAllParams());

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

        $this->_helper->json(['success' => true, 'data' => $jsonOrders, 'count' => count($jsonOrders), 'total' => $list->getTotalCount()]);
    }

    protected function prepareOrder(\CoreShop\Model\Order $order)
    {
        $date = "";

        if ($order->getOrderDate() instanceof \Pimcore\Date) {
            $date = intval($order->getOrderDate()->get(\Zend_Date::TIMESTAMP));
        } elseif ($order->getOrderDate() instanceof \Carbon\Carbon) {
            $date = intval($order->getOrderDate()->getTimestamp());
        }

        $element = [
            'o_id' => $order->getId(),
            'orderState' => \CoreShop\Model\Order\State::getOrderCurrentState($order),
            'orderDate' => $date,
            'orderNumber' => $order->getOrderNumber(),
            'lang' => $order->getLang(),
            'carrier' => $order->getCarrier() instanceof \CoreShop\Model\Carrier ? $order->getCarrier()->getId() : null,
            'discount' => $order->getDiscount(),
            'subtotal' => $order->getSubtotal(),
            'shipping' => $order->getShipping(),
            'paymentFee' => $order->getPaymentFee(),
            'totalTax' => $order->getTotalTax(),
            'total' => $order->getTotal(),
            'currency' => $this->getCurrency($order->getCurrency() ? $order->getCurrency() : \CoreShop::getTools()->getCurrency()),
            'shop' => $order->getShop() instanceof \CoreShop\Model\Shop ? $order->getShop()->getId() : null
        ];

        return $element;
    }

    public function getPaymentProvidersAction()
    {
        $providers = \CoreShop::getPaymentProviders();
        $result = [];

        foreach ($providers as $provider) {
            if ($provider instanceof \CoreShop\Model\Plugin\Payment) {
                $result[] = [
                    'name' => $provider->getName(),
                    'id' => $provider->getIdentifier(),
                ];
            }
        }

        $this->_helper->json(['success' => true, 'data' => $result]);
    }

    public function addPaymentAction()
    {
        //@TODO: Add translations for messages

        $orderId = $this->getParam('o_id');
        $order = \CoreShop\Model\Order::getById($orderId);
        $amount = doubleval($this->getParam('amount', 0));
        $transactionId = $this->getParam('transactionNumber');
        $paymentProviderName = $this->getParam('paymentProvider');

        if (!$order instanceof \CoreShop\Model\Order) {
            $this->_helper->json(['success' => false, 'message' => 'Order with ID "'.$orderId.'" not found']);
        }

        $paymentProvider = \CoreShop::getPaymentProvider($paymentProviderName);

        if ($paymentProvider instanceof \CoreShop\Model\Plugin\Payment) {
            $payedTotal = $order->getPayedTotal();

            $payedTotal += $amount;

            if ($payedTotal > $order->getTotal()) {
                $this->_helper->json(['success' => false, 'message' => 'Payed Amount is greater than order amount']);
            } else {
                $order->createPayment($paymentProvider, $amount, true, $transactionId);
                $this->_helper->json(['success' => true, 'payments' => $this->getPayments($order), 'totalPayed' => $order->getPayedTotal()]);
            }
        } else {
            $this->_helper->json(['success' => false, 'message' => "Payment Provider '$paymentProviderName' not found"]);
        }
    }

    public function sendMessageAction()
    {
        $orderId = $this->getParam('o_id');
        $order = \CoreShop\Model\Order::getById($orderId);
        $messageText = $this->getParam('message', '');

        if (!$order instanceof \CoreShop\Model\Order) {
            $this->_helper->json(['success' => false, 'message' => "Order with ID '$orderId' not found"]);
        }

        if (strlen($messageText) <= 0) {
            $this->_helper->json(['success' => false, 'message' => 'No Message text set']);
        }

        $salesContact = \CoreShop\Model\Messaging\Contact::getById(\CoreShop\Model\Configuration::get("SYSTEM.MESSAGING.CONTACT.SALES"));
        $thread = \CoreShop\Model\Messaging\Thread::searchThread($order->getCustomer()->getEmail(), $salesContact->getId(), \CoreShop\Model\Shop::getShop()->getId(), $orderId);

        if (!$thread instanceof \CoreShop\Model\Messaging\Thread) {
            $thread = CoreShop\Model\Messaging\Thread::create();
            $thread->setLanguage($order->getLang());
            $thread->setStatusId(\CoreShop\Model\Configuration::get('SYSTEM.MESSAGING.THREAD.STATE.NEW'));
            $thread->setEmail($order->getCustomer()->getEmail());
            $thread->setUser($order->getCustomer());
            $thread->setContact($salesContact);
            $thread->setShopId($order->getShop()->getId());
            $thread->setToken(uniqid());
            $thread->setOrder($order);
            $thread->save();
        }

        $message = $thread->createMessage($messageText);

        $message->sendNotification('customer-reply', $thread->getEmail());

        $this->_helper->json(['success' => true]);
    }

    public function changeOrderItemAction()
    {
        $orderId = $this->getParam('id');
        $orderItemId = $this->getParam("orderItemId");
        $amount = $this->getParam("amount");
        $price = $this->getParam("price");

        $order = \CoreShop\Model\Order::getById($orderId);
        $orderItem = \CoreShop\Model\Order\Item::getById($orderItemId);

        if (!$order instanceof \CoreShop\Model\Order) {
            $this->_helper->json(['success' => false, 'message' => "Order with ID '$orderId' not found"]);
        }

        if (!$orderItem instanceof \CoreShop\Model\Order\Item) {
            $this->_helper->json(['success' => false, 'message' => "OrderItem with ID '$orderItemId' not found"]);
        }

        $order->updateOrderItem($orderItem, $amount, $price);

        $this->_helper->json(['success' => true, "summary" => $this->getSummary($order), "details" => $this->getDetails($order), "total" => $order->getTotal()]);
    }

    /**
     * @deprecated
     */
    public function resendOrderStateMailAction()
    {
        $orderId = $this->getParam('id');
        $orderStateId = $this->getParam('orderStateId');
        $order = \CoreShop\Model\Order::getById($orderId);
        $orderState = \CoreShop\Model\Order\State::getById($orderStateId);

        if (!$order instanceof \CoreShop\Model\Order) {
            $this->_helper->json(['success' => false, 'message' => "Order with ID '$orderId' not found"]);
        }

        if (!$orderState instanceof \CoreShop\Model\Order\State) {
            $this->_helper->json(['success' => false, 'message' => "OrderState with ID '$orderStateId' not found"]);
        }

        if ($orderState->getEmail()) {
            $orderStateMailDocument = \Pimcore\Model\Document::getByPath($orderState->getEmailDocument($order->getLang()));

            if ($orderStateMailDocument instanceof \Pimcore\Model\Document\Email) {
                $mail = new \CoreShop\Mail();
                $mail->sendOrderMail($orderStateMailDocument, $order, $orderState);

                $this->_helper->json(['success' => true]);
            } else {
                $this->_helper->json(['success' => false, 'message' => 'coreshop_order_state_document_not_found']);
            }
        }

        $this->_helper->json(['success' => false, 'message' => 'coreshop_order_state_has_no_email']);
    }

    public function detailAction()
    {
        $orderId = $this->getParam('id');
        $order = \CoreShop\Model\Order::getById($orderId);

        if (!$order instanceof \CoreShop\Model\Order) {
            $this->_helper->json(['success' => false, 'message' => "Order with ID '$orderId' not found"]);
        }

        $jsonOrder = $this->getDataForObject($order);

        if ($jsonOrder['items'] === null) {
            $jsonOrder['items'] = [];
        }

        $jsonOrder['o_id'] = $order->getId();
        $jsonOrder['customer'] = $order->getCustomer() instanceof \CoreShop\Model\Base ? $this->getDataForObject($order->getCustomer()) : null;
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
        $jsonOrder['currency'] = $this->getCurrency($order->getCurrency() ? $order->getCurrency() : \CoreShop::getTools()->getCurrency());
        $jsonOrder['shop'] = $order->getShop() instanceof \CoreShop\Model\Shop ? $order->getShop()->getObjectVars() : null;
        $jsonOrder['visitor'] = \CoreShop\Model\Visitor::getById($order->getVisitorId());

        $jsonOrder['address'] = [
            'shipping' => $this->getDataForObject($order->getShippingAddress()),
            'billing' => $this->getDataForObject($order->getBillingAddress())
        ];
        $jsonOrder['shipping'] = [
            'carrier' => $order->getCarrier() instanceof \CoreShop\Model\Carrier ? $order->getCarrier()->getName() : null,
            'weight' => $order->getTotalWeight(),
            'cost' => $order->getShipping(),
            'tracking' => $order->getTrackingCode()
        ];

        $jsonOrder['priceRule'] = false;

        if ($order->getPriceRuleFieldCollection() instanceof Object\Fieldcollection) {
            $rules = [];

            foreach ($order->getPriceRuleFieldCollection()->getItems() as $ruleItem) {
                if ($ruleItem instanceof \CoreShop\Model\PriceRule\Item) {
                    $rule = $ruleItem->getPriceRule();

                    if ($rule instanceof \CoreShop\Model\Cart\PriceRule) {
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

        $this->_helper->json(["success" => true, "order" => $jsonOrder]);
    }

    public function getAddressFieldsAction()
    {
        $orderId = $this->getParam('id');
        $order = \CoreShop\Model\Order::getById($orderId);
        $addressType = $this->getParam('type');

        if (!$order instanceof \CoreShop\Model\Order) {
            $this->_helper->json(['success' => false, 'message' => "Order with ID '$orderId' not found"]);
        }

        $addressClassId = \CoreShop\Model\User\Address::classId();

        $fieldCollection = \Pimcore\Model\Object\ClassDefinition::getById($addressClassId);

        if ($fieldCollection instanceof \Pimcore\Model\Object\ClassDefinition) {
            $this->_helper->json([
                'success' => true,
                'data' => $addressType == 'shipping' ? $this->getDataForObject($order->getShippingAddress()) : $this->getDataForObject($order->getBillingAddress()),
                'layout' => $fieldCollection->getLayoutDefinitions()
            ]);
        }

        $this->_helper->json(['success' => true]);
    }

    public function changeAddressAction()
    {
        $orderId = $this->getParam('id');
        $order = \CoreShop\Model\Order::getById($orderId);
        $addressType = $this->getParam('type');
        $data = $this->getAllParams();

        if (!$order instanceof \CoreShop\Model\Order) {
            $this->_helper->json(['success' => false, 'message' => "Order with ID '$orderId' not found"]);
        }

        $address = $addressType == 'shipping' ? $order->getShippingAddress() : $order->getBillingAddress();

        unset($data['action']);
        unset($data['module']);
        unset($data['controller']);
        unset($data['type']);
        unset($data['_dc']);
        unset($data['id']);

        if ($address instanceof \CoreShop\Model\User\Address) {
            $address->setValues($data);
            $address->save();

            if ($order->getProperty('invoice') instanceof \Pimcore\Model\Asset) {
                foreach ($order->getInvoices() as $invoice) {
                    $invoice->generate();
                }
            }

            $this->_helper->json(['success' => true]);
        }

        $this->_helper->json(['success' => false]);
    }

    public function getCustomerDetailsAction()
    {
        $customerId = $this->getParam("customerId");
        $user = \CoreShop\Model\User::getById($customerId);

        if (!$user instanceof \CoreShop\Model\User) {
            $this->_helper->json(['success' => false, 'message' => "Customer with ID '$customerId' not found"]);
        }

        $this->_helper->json(['success' => true, 'customer' => $this->getDataForObject($user)]);
    }

    public function getCustomerCartsAction()
    {
        $customerId = $this->getParam("customerId");
        $user = \CoreShop\Model\User::getById($customerId);

        if (!$user instanceof \CoreShop\Model\User) {
            $this->_helper->json(['success' => false, 'message' => "Customer with ID '$customerId' not found"]);
        }

        $manager = new \CoreShop\Model\Cart\Manager();
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
                "currency" => $this->getCurrency(\CoreShop::getTools()->getBaseCurrency()),
                "productIds" => $productIds
            ];
        }

        $this->_helper->json(['success' => true, 'carts' => $result]);
    }

    public function getCustomerOrdersAction()
    {
        $customerId = $this->getParam("customerId");
        $user = \CoreShop\Model\User::getById($customerId);

        if (!$user instanceof \CoreShop\Model\User) {
            $this->_helper->json(['success' => false, 'message' => "Customer with ID '$customerId' not found"]);
        }

        $orders = $user->getOrders();
        $result = [];

        foreach ($orders as $order) {
            if ($order instanceof \CoreShop\Model\Order) {
                $productIds = [];

                foreach ($order->getItems() as $item) {
                    if ($item->getProduct() instanceof \CoreShop\Model\Product) {
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

        $this->_helper->json(['success' => true, 'orders' => $result]);
    }

    public function getProductDetailsAction()
    {
        $productIds = \Zend_Json::decode($this->getParam("products"));
        $currency = \CoreShop\Model\Currency::getById($this->getParam("currency"));

        $result = [];

        foreach ($productIds as $productObject) {
            $productId = $productObject['id'];

            $product = \CoreShop\Model\Product::getById($productId);

            if ($product instanceof \CoreShop\Model\Product) {
                $productFlat = $this->getDataForObject($product);

                $productFlat['amount'] = $productObject['amount'];

                $productFlat['price'] = \CoreShop::getTools()->convertToCurrency($product->getPrice(true, false), $currency);
                $result[] = $productFlat;
            }
        }

        $this->_helper->json(['success' => true, 'products' => $result]);
    }

    public function getCarriersDetailsAction()
    {
        $productIds = \Zend_Json::decode($this->getParam("products"));
        $customerId = $this->getParam("customerId");
        $shippingAddressId = $this->getParam("shippingAddress");
        $billingAddressId = $this->getParam("billingAddress");

        //$language = $this->getParma("language");
        $currency = \CoreShop\Model\Currency::getById($this->getParam("currency"));

        $user = \CoreShop\Model\User::getById($customerId);
        $shippingAddress = \CoreShop\Model\User\Address::getById($shippingAddressId);
        $billingAddress = \CoreShop\Model\User\Address::getById($billingAddressId);

        $result = [];

        if (!$user instanceof \CoreShop\Model\User) {
            $this->_helper->json(['success' => false, 'message' => "Customer with ID '$customerId' not found"]);
        }

        if (!$shippingAddress instanceof \CoreShop\Model\User\Address) {
            $this->_helper->json(['success' => false, 'message' => "Address with ID '$shippingAddressId' not found"]);
        }

        if (!$billingAddress instanceof \CoreShop\Model\User\Address) {
            $this->_helper->json(['success' => false, 'message' => "Address with ID '$billingAddressId' not found"]);
        }

        $cart = $this->createTempCart($user, $shippingAddress, $billingAddress, $currency, $productIds);

        $carriers = \CoreShop\Model\Carrier::getCarriersForCart($cart, $cart->getShippingAddress());

        foreach ($carriers as $carrier) {
            $price = $carrier->getDeliveryPrice($cart, true, $cart->getShippingAddress());

            $result[] = [
                "id" => $carrier->getId(),
                "name" => $carrier->getName(),
                "price" => \CoreShop::getTools()->convertToCurrency($price, $currency)
            ];
        }

        $cart->delete();

        $this->_helper->json(['success' => true, 'carriers' => $result]);
    }

    public function getOrderTotalAction()
    {
        $productIds = \Zend_Json::decode($this->getParam('products'));
        $customerId = $this->getParam('customerId');
        $shippingAddressId = $this->getParam('shippingAddress');
        $billingAddressId = $this->getParam('billingAddress');
        $carrierId = $this->getParam('carrier');
        $freeShipping = $this->getParam('freeShipping');

        //$language = $this->getParma('language');
        $currency = \CoreShop\Model\Currency::getById($this->getParam('currency'));

        $user = \CoreShop\Model\User::getById($customerId);
        $shippingAddress = \CoreShop\Model\User\Address::getById($shippingAddressId);
        $billingAddress = \CoreShop\Model\User\Address::getById($billingAddressId);
        $carrier = \CoreShop\Model\Carrier::getById($carrierId);

        if (!$user instanceof \CoreShop\Model\User) {
            $this->_helper->json(['success' => false, 'message' => "Customer with ID '$customerId' not found"]);
        }

        if (!$shippingAddress instanceof \CoreShop\Model\User\Address) {
            $this->_helper->json(['success' => false, 'message' => "Address with ID '$shippingAddressId' not found"]);
        }

        if (!$billingAddress instanceof \CoreShop\Model\User\Address) {
            $this->_helper->json(['success' => false, 'message' => "Address with ID '$billingAddressId' not found"]);
        }

        if (!$carrier instanceof \CoreShop\Model\Carrier) {
            $this->_helper->json(['success' => false, 'message' => "Carrier with ID '$carrierId' not found"]);
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

        $this->_helper->json(['success' => true, 'summary' => $values]);
    }

    public function createOrderAction()
    {
        $productIds = \Zend_Json::decode($this->getParam('products'));
        $customerId = $this->getParam('customerId');
        $shippingAddressId = $this->getParam('shippingAddress');
        $billingAddressId = $this->getParam('billingAddress');
        $carrierId = $this->getParam('carrier');
        $freeShipping = $this->getParam('freeShipping');
        $paymentModuleName = $this->getParam('paymentProvider');
        $shopId = $this->getParam('shop');

        $language = $this->getParam('language');
        $currency = \CoreShop\Model\Currency::getById($this->getParam('currency'));

        $user = \CoreShop\Model\User::getById($customerId);
        $shippingAddress = \CoreShop\Model\User\Address::getById($shippingAddressId);
        $billingAddress = \CoreShop\Model\User\Address::getById($billingAddressId);
        $carrier = \CoreShop\Model\Carrier::getById($carrierId);
        $paymentModule = \CoreShop::getPaymentProvider($paymentModuleName);
        $shop = \CoreShop\Model\Shop::getById($shopId);

        if (!$user instanceof \CoreShop\Model\User) {
            $this->_helper->json(['success' => false, 'message' => "Customer with ID '$customerId' not found"]);
        }

        if (!$shippingAddress instanceof \CoreShop\Model\User\Address) {
            $this->_helper->json(['success' => false, 'message' => "Address with ID '$shippingAddressId' not found"]);
        }

        if (!$billingAddress instanceof \CoreShop\Model\User\Address) {
            $this->_helper->json(['success' => false, 'message' => "Address with ID '$billingAddressId' not found"]);
        }

        if (!$carrier instanceof \CoreShop\Model\Carrier) {
            $this->_helper->json(['success' => false, 'message' => "Carrier with ID '$carrierId' not found"]);
        }

        if (!$paymentModule instanceof \CoreShop\Model\Plugin\Payment) {
            $this->_helper->json(['success' => false, 'message' => "Payment Module with ID '$paymentModuleName' not found"]);
        }

        if (!$shop instanceof \CoreShop\Model\Shop) {
            $this->_helper->json(['success' => false, 'message' => "Shop with ID '$shopId' not found"]);
        }

        $cart = $this->createTempCart($user, $shippingAddress, $billingAddress, $currency, $productIds);
        $cart->setCarrier($carrier);
        $cart->setShop($shop);
        $cart->setFreeShipping($freeShipping);
        $cart->save();

        $order = $cart->createOrder($paymentModule, 0, $language);

        $cart->delete();

        $this->_helper->json(['success' => true, 'orderId' => $order->getId()]);
    }

    /**
     * @param $shippingAddress
     * @param $billingAddress
     * @param $currency
     * @param $productIds
     * @return \CoreShop\Model\Cart
     */
    protected function createTempCart($user, $shippingAddress, $billingAddress, $currency, $productIds)
    {
        $cart = \CoreShop\Model\Cart::create();
        $cart->setParent(\Pimcore\Model\Object\Service::createFolderByPath("/coreshop/tmp"));
        $cart->setKey(uniqid());
        $cart->setShippingAddress($shippingAddress);
        $cart->setBillingAddress($billingAddress);
        $cart->setCurrency($currency);
        $cart->setUser($user);
        //$cart->setCurrency($currency); needs a CORE Change -> Cart should be responsible for the currency...
        //$cart->setLanguage($language);
        $cart->save();

        foreach ($productIds as $productObject) {
            $productId = $productObject['id'];

            $product = \CoreShop\Model\Product::getById($productId);

            if ($product instanceof \CoreShop\Model\Product) {
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
     * @param \CoreShop\Model\Order $order
     * @return array
     */
    protected function getStatesHistory(\CoreShop\Model\Order $order)
    {
        //Get History
        $history = \CoreShop\Model\Order\State::getOrderStateHistory($order);

        // create timeline
        $statesHistory = [];

        $date = new \Pimcore\Date();
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

        return $statesHistory;
    }

    /**
     * @param \CoreShop\Model\Order $order
     * @return array
     * @throws \CoreShop\Exception\UnsupportedException
     */
    protected function getPayments(\CoreShop\Model\Order $order)
    {
        $payments = $order->getPayments();
        $return = [];

        foreach ($payments as $payment) {
            $noteList = new \Pimcore\Model\Element\Note\Listing();
            $noteList->addConditionParam('type = ?', \CoreShop\Model\Order\Payment::NOTE_TRANSACTION);
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
     * @param \CoreShop\Model\Order $order
     * @return array
     * @throws \CoreShop\Exception\UnsupportedException
     */
    protected function getDetails(\CoreShop\Model\Order $order)
    {
        $details = $order->getItems();
        $items = [];

        foreach ($details as $detail) {
            $items[] = [
                'o_id' => $detail->getId(),
                'product' => $detail->getProduct() instanceof \CoreShop\Model\Product ? $detail->getProduct()->getId() : null,
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
     * @param \CoreShop\Model\Order $order
     * @return array
     * @throws \CoreShop\Exception\UnsupportedException
     */
    protected function getSummary(\CoreShop\Model\Order $order)
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

        foreach ($taxes as $tax) {
            if ($tax instanceof \CoreShop\Model\Order\Tax) {
                $summary[] = [
                    'key' => 'tax_' . $tax->getName(),
                    'text' => sprintf($this->view->translateAdmin('Tax (%s - %s)'), $tax->getName(), \CoreShop::getTools()->formatTax($tax->getRate())),
                    'value' => $tax->getAmount()
                ];
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
     * @param \CoreShop\Model\Order $order
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
     * @param \CoreShop\Model\Order $order
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
     * @param $order
     *
     * @return array
     */
    protected function getMailCorrespondence($order)
    {
        $list = [];

        $noteList = new \Pimcore\Model\Element\Note\Listing();
        $noteList->addConditionParam('type = ?', \CoreShop\Model\Order::NOTE_EMAIL);
        $noteList->addConditionParam('cid = ?', $order->getId());
        $noteList->setOrderKey('date');
        $noteList->setOrder('desc');

        $objects = $noteList->load();

        foreach ($objects as $note) {

            $subject = null;
            $recipient = null;

            if( isset($note->data['subject'])) {
                $subject = $note->data['subject']['data'];
            }
            if( isset($note->data['recipient'])) {
                $recipient = $note->data['recipient']['data'];
            }

            $list[] = [
                'emailId' => (int) $note->data['document']['data'],
                'date' => $note->date,
                'description' => $note->description,
                'subject' => $subject,
                'recipient' => $recipient
            ];
        }

        return $list;
    }

    /**
     * @param \CoreShop\Model\Currency $currency
     * @return array
     */
    protected function getCurrency(CoreShop\Model\Currency $currency)
    {
        return [
            'name' => $currency->getName(),
            'symbol' => $currency->getSymbol()
        ];
    }
}
