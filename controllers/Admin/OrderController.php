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
            $conditionFilters[] = \Pimcore\Model\Object\Service::getFilterCondition($this->getParam('filter'), \Pimcore\Model\Object\ClassDefinition::getByName('CoreShopOrder'));
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
        $jsonOrders = array();

        foreach ($orders as $order) {
            $jsonOrders[] = $this->prepareOrder($order);
        }

        $this->_helper->json(array('success' => true, 'data' => $jsonOrders, 'count' => count($jsonOrders), 'total' => $list->getTotalCount()));
    }

    protected function prepareOrder(\CoreShop\Model\Order $order)
    {
        $date = "";

        if($order->getOrderDate() instanceof \Pimcore\Date) {
            $date = intval($order->getOrderDate()->get(\Zend_Date::TIMESTAMP));
        }
        else if($order->getOrderDate() instanceof \Carbon\Carbon) {
            $date = intval($order->getOrderDate()->getTimestamp());
        }

        $element = array(
            'o_id' => $order->getId(),
            'orderState' => $order->getOrderState() instanceof \CoreShop\Model\Order\State ? $order->getOrderState()->getId() : null,
            'orderDate' => $date,
            'orderNumber' => $order->getOrderNumber(),
            'lang' => $order->getLang(),
            'carrier' => $order->getCarrier() instanceof \CoreShop\Model\Carrier ? $order->getCarrier()->getId() : null,
            'priceRule' => $order->getPriceRule() instanceof \CoreShop\Model\Cart\PriceRule ? $order->getPriceRule()->getId() : null,
            'discount' => $order->getDiscount(),
            'subtotal' => $order->getSubtotal(),
            'shipping' => $order->getShipping(),
            'paymentFee' => $order->getPaymentFee(),
            'totalTax' => $order->getTotalTax(),
            'total' => $order->getTotal(),
            'currency' => $this->getCurrency($order->getCurrency() ? $order->getCurrency() : \CoreShop::getTools()->getCurrency()),
            'shop' => $order->getShop() instanceof \CoreShop\Model\Shop ? $order->getShop()->getId() : null
        );

        return $element;
    }

    public function getInvoiceForOrderAction()
    {
        $orderId = $this->getParam('id');
        $order = \CoreShop\Model\Order::getById($orderId);

        if ($order instanceof \CoreShop\Model\Order) {
            $invoice = $order->getProperty('invoice');

            if ($invoice instanceof \Pimcore\Model\Asset\Document) {
                $this->_helper->json(array('success' => true, 'assetId' => $invoice->getId()));
            }
        }

        $this->_helper->json(array('success' => false));
    }

    public function getPaymentProvidersAction()
    {
        $providers = \CoreShop::getPaymentProviders();
        $result = array();

        foreach ($providers as $provider) {
            if ($provider instanceof \CoreShop\Model\Plugin\Payment) {
                $result[] = array(
                    'name' => $provider->getName(),
                    'id' => $provider->getIdentifier(),
                );
            }
        }

        $this->_helper->json(array('success' => true, 'data' => $result));
    }

    public function addPaymentAction()
    {
        //@TODO: Add translations for messages

        $orderId = $this->getParam('o_id');
        $order = \CoreShop\Model\Order::getById($orderId);
        $amount = doubleval($this->getParam('amount', 0));
        $paymentProviderName = $this->getParam('paymentProvider');

        if (!$order instanceof \CoreShop\Model\Order) {
            $this->_helper->json(array('success' => false, 'message' => "Order with ID '$orderId' not found"));
        }

        $paymentProvider = \CoreShop::getPaymentProvider($paymentProviderName);

        if ($paymentProvider instanceof \CoreShop\Model\Plugin\Payment) {
            $payedTotal = $order->getPayedTotal();

            $payedTotal += $amount;

            if ($payedTotal > $order->getTotal()) {
                $this->_helper->json(array('success' => false, 'message' => 'Payed Amount is greater than order amount'));
            } else {
                $order->createPayment($paymentProvider, $amount, true);

                $this->_helper->json(array('success' => true, "payments" => $this->getPayments($order), "totalPayed" => $order->getPayedTotal()));
            }
        } else {
            $this->_helper->json(array('success' => false, 'message' => "Payment Provider '$paymentProviderName' not found"));
        }
    }

    public function sendMessageAction()
    {
        $orderId = $this->getParam('o_id');
        $order = \CoreShop\Model\Order::getById($orderId);
        $messageText = $this->getParam('message', '');

        if (!$order instanceof \CoreShop\Model\Order) {
            $this->_helper->json(array('success' => false, 'message' => "Order with ID '$orderId' not found"));
        }

        if (strlen($messageText) <= 0) {
            $this->_helper->json(array('success' => false, 'message' => 'No Message text set'));
        }

        $salesContact = \CoreShop\Model\Messaging\Contact::getById(\CoreShop\Model\Configuration::get("SYSTEM.MESSAGING.CONTACT.SALES"));
        $thread = \CoreShop\Model\Messaging\Thread::searchThread($order->getCustomer()->getEmail(), $salesContact->getId(), \CoreShop\Model\Shop::getShop()->getId(), $orderId);

        if (!$thread instanceof \CoreShop\Model\Messaging\Thread) {
            $thread = new CoreShop\Model\Messaging\Thread();
            $thread->setLanguage($order->getLang());
            $thread->setStatusId(\CoreShop\Model\Configuration::get('SYSTEM.MESSAGING.THREAD.STATE.NEW'));
            $thread->setEmail($order->getCustomer()->getEmail());
            $thread->setUser($order->getCustomer());
            $thread->setContact($salesContact);
            $thread->setToken(uniqid());
            $thread->setOrder($order);
            $thread->save();
        }

        $message = $thread->createMessage($messageText);

        $customerInfoMail = \Pimcore\Model\Document\Email::getById(\CoreShop\Model\Configuration::get('SYSTEM.MESSAGING.MAIL.CUSTOMER.RE.'.strtoupper($thread->getLanguage())));
        $message->sendNotification($customerInfoMail, $thread->getEmail());

        $this->_helper->json(array('success' => true));
    }

    public function changeOrderStateAction()
    {
        $orderId = $this->getParam('id');
        $orderStateId = $this->getParam('orderStateId');
        $order = \CoreShop\Model\Order::getById($orderId);
        $orderState = \CoreShop\Model\Order\State::getById($orderStateId);

        if (!$order instanceof \CoreShop\Model\Order) {
            $this->_helper->json(array('success' => false, 'message' => "Order with ID '$orderId' not found"));
        }

        if (!$orderState instanceof \CoreShop\Model\Order\State) {
            $this->_helper->json(array('success' => false, 'message' => "OrderState with ID '$orderStateId' not found"));
        }

        $orderState->processStep($order);

        $this->_helper->json(array("success" => true, "statesHistory" => $this->getStatesHistory($order)));
    }

    public function changeTrackingCodeAction()
    {
        $orderId = $this->getParam('id');
        $trackingCode = $this->getParam("trackingCode");

        $order = \CoreShop\Model\Order::getById($orderId);

        if (!$order instanceof \CoreShop\Model\Order) {
            $this->_helper->json(array('success' => false, 'message' => "Order with ID '$orderId' not found"));
        }

        if (!$trackingCode || $order->getTrackingCode() === $trackingCode) {
            $this->_helper->json(array('success' => false, 'message' => "Tracking code did not change or is empty"));
        }

        $order->setTrackingCode($trackingCode);
        $order->save();

        $this->_helper->json(array('success' => true));
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
            $this->_helper->json(array('success' => false, 'message' => "Order with ID '$orderId' not found"));
        }

        if (!$orderItem instanceof \CoreShop\Model\Order\Item) {
            $this->_helper->json(array('success' => false, 'message' => "OrderItem with ID '$orderItemId' not found"));
        }

        $order->updateOrderItem($orderItem, $amount, $price);

        $this->_helper->json(array('success' => true, "summary" => $this->getSummary($order), "details" => $this->getDetails($order), "total" => $order->getTotal()));
    }

    public function resendOrderStateMailAction()
    {
        $orderId = $this->getParam('id');
        $orderStateId = $this->getParam('orderStateId');
        $order = \CoreShop\Model\Order::getById($orderId);
        $orderState = \CoreShop\Model\Order\State::getById($orderStateId);

        if (!$order instanceof \CoreShop\Model\Order) {
            $this->_helper->json(array('success' => false, 'message' => "Order with ID '$orderId' not found"));
        }

        if (!$orderState instanceof \CoreShop\Model\Order\State) {
            $this->_helper->json(array('success' => false, 'message' => "OrderState with ID '$orderStateId' not found"));
        }

        if ($orderState->getEmail()) {
            $orderStateMailDocument = \Pimcore\Model\Document::getByPath($orderState->getEmailDocument($order->getLang()));

            if ($orderStateMailDocument instanceof \Pimcore\Model\Document\Email) {
                $mail = new \CoreShop\Mail();
                $mail->sendOrderMail($orderStateMailDocument, $order, $orderState);

                $this->_helper->json(array('success' => true));
            } else {
                $this->_helper->json(array('success' => false, 'message' => 'coreshop_order_state_document_not_found'));
            }
        }

        $this->_helper->json(array('success' => false, 'message' => 'coreshop_order_state_has_no_email'));
    }

    public function detailAction()
    {
        $orderId = $this->getParam('id');
        $order = \CoreShop\Model\Order::getById($orderId);

        if (!$order instanceof \CoreShop\Model\Order) {
            $this->_helper->json(array('success' => false, 'message' => "Order with ID '$orderId' not found"));
        }

        $jsonOrder = $this->getDataForObject($order);

        //$jsonOrder = $order->getObjectVars();
        //$jsonOrder = [];

        if($jsonOrder['items'] === null) {
            $jsonOrder['items'] = [];
        }

        $jsonOrder['o_id'] = $order->getId();
        $jsonOrder['customer'] = $order->getCustomer() instanceof \CoreShop\Model\Base ? $this->getDataForObject($order->getCustomer()) : null;
        $jsonOrder['statesHistory'] = $this->getStatesHistory($order);
        $jsonOrder['invoice'] = $order->getProperty("invoice");
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

        $jsonOrder['payments'] = $this->getPayments($order);
        $jsonOrder['totalPayed'] = $order->getPayedTotal();
        $jsonOrder['details'] = $this->getDetails($order);
        $jsonOrder['summary'] = $this->getSummary($order);
        $jsonOrder['currency'] = $this->getCurrency($order->getCurrency() ? $order->getCurrency() : \CoreShop::getTools()->getCurrency());
        $jsonOrder['shop'] = $order->getShop() instanceof \CoreShop\Model\Shop ? $order->getShop()->getObjectVars() : null;

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

        $this->_helper->json(array("success" => true, "order" => $jsonOrder));
    }

    public function getAddressFieldsAction()
    {
        $orderId = $this->getParam('id');
        $order = \CoreShop\Model\Order::getById($orderId);
        $addressType = $this->getParam('type');

        if (!$order instanceof \CoreShop\Model\Order) {
            $this->_helper->json(array('success' => false, 'message' => "Order with ID '$orderId' not found"));
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
            $this->_helper->json(array('success' => false, 'message' => "Order with ID '$orderId' not found"));
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
                $order->getInvoice(true);
            }

            $this->_helper->json(array('success' => true));
        }

        $this->_helper->json(array('success' => false));
    }

    /**
     * @param Object\Concrete $data
     * @return array
     */
    private function getDataForObject(Object\Concrete $data)
    {
        $objectData = [];

        foreach ($data->getClass()->getFieldDefinitions() as $key => $def) {
            $getter = "get" . ucfirst($key);
            $fieldData = $data->$getter();

            if ($def instanceof Object\ClassDefinition\Data) {
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
        $history = $order->getOrderStateHistory();

        // create timeline
        $statesHistory = array();

        $date = new \Pimcore\Date();
        foreach ($history as $note) {
            $user = $user = \Pimcore\Model\User::getById($note->getUser());
            $avatar = $user ? sprintf('/admin/user/get-image?id=%d', $user->getId()) : null;

            $fromState = $note->getData()['fromState']['data'];
            $toState = $note->getData()['toState']['data'];

            $statesHistory[] = [
                'icon' => 'coreshop_icon_orderstates',
                'type' => $note->getType(),
                'date' => $date->setTimestamp($note->getDate())->get(\Pimcore\Date::DATETIME_MEDIUM),
                'avatar' => $avatar,
                'user' => $user ? $user->getName() : null,
                'description' => $note->getDescription(),
                'title' => $note->getTitle(),
                'toState' => $toState,
                'fromState' => $fromState
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
            $return[] = [
                "datePayment" => $payment->getDatePayment() ? $payment->getDatePayment()->getTimestamp() : "",
                "provider" => $payment->getProvider(),
                "transactionIdentifier" => $payment->getTransactionIdentifier(),
                "amount" => $payment->getAmount()
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
                "o_id" => $detail->getId(),
                "product" => $detail->getProduct() instanceof \CoreShop\Model\Product ? $detail->getProduct()->getId() : null,
                "product_name" => $detail->getProductName(),
                "product_image" => ($detail->getProductImage() instanceof \Pimcore\Model\Asset\Image) ? $detail->getProductImage()->getPath() : null,
                "wholesale_price" => $detail->getWholesalePrice(),
                "price_without_tax" => $detail->getPriceWithoutTax(),
                "price" => $detail->getPrice(),
                "amount" => $detail->getAmount(),
                "total" => $detail->getTotal(),
                "total_tax" => $detail->getTotalTax()
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
                "key" => "discount",
                "value" => $order->getDiscount()
            ];
        }

        if ($order->getShipping() > 0) {
            $summary[] = [
                "key" => "shipping",
                "value" => $order->getShipping()
            ];

            $summary[] = [
                "key" => "shipping_tax",
                "value" => $order->getShippingTax()
            ];
        }

        if ($order->getPaymentFee() > 0) {
            $summary[] = [
                "key" => "payment",
                "value" => $order->getPaymentFee()
            ];
        }

        $summary[] = [
            "key" => "total_tax",
            "value" => $order->getTotalTax()
        ];
        $summary[] = [
            "key" => "total",
            "value" => $order->getTotal()
        ];

        return $summary;
    }

    /**
     * @param \CoreShop\Model\Currency $currency
     * @return array
     */
    protected function getCurrency(CoreShop\Model\Currency $currency)
    {
        return [
            "name" => $currency->getName(),
            "symbol" => $currency->getSymbol()
        ];
    }
}
