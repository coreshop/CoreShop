<?php

namespace CoreShop\Bundle\CoreBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\AdminController;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use Pimcore\Model\Object\ClassDefinition;
use Pimcore\Model\Object\Concrete;
use Pimcore\Model\Object;
use Symfony\Component\HttpFoundation\Request;

/**
 * @todo: maybe we should move this one to the AdminBundle?
 */
class OrderController extends AdminController
{
    public function getOrderGridConfigurationAction()
    {
        $defaultConfiguration = [
            [
                'text' => 'coreshop_store',
                'type' => 'integer',
                'dataIndex' => 'store',
                'renderAs' => 'store',
                'filter' => [
                    'type' => 'number'
                ]
            ],
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
            /*[
                'text' => 'coreshop_orders_orderState',
                'type' => null,
                'dataIndex' => 'orderState',
                'renderAs' => 'orderState',
                'width' => 200
            ],*/
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

        $addressClassId = $this->getParameter('coreshop.model.address.pimcore_class_id');

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

        foreach (['shipping', 'invoice'] as $type) {
            foreach ($addressFields as $fieldElement) {
                $name = $fieldElement['fieldName'];
                $dataIndex = $fieldElement['dataIndex'];

                $fieldElement['text'] = 'coreshop_address_'.$type.'|[' . $name . ']';
                $fieldElement['dataIndex'] = 'address' . ucfirst($type) . ucfirst($dataIndex);

                $defaultConfiguration[] = $fieldElement;
            }
        }


        return $this->json(["success" => true, "columns" => $defaultConfiguration]);
    }

    public function getOrders(Request $request) {
        $list = $this->getOrderList();
        $list->setLimit($request->get('limit', 30));
        $list->setOffset($request->get('page', 1) - 1);

        if ($request->get('filter', null)) {
            $conditionFilters = [];
            $conditionFilters[] = \Pimcore\Model\Object\Service::getFilterCondition($this->getParam('filter'), \Pimcore\Model\Object\ClassDefinition::getById($this->getParameter('coreshop.model.order.pimcore_class_id')));
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
     * @param OrderInterface $order
     * @return array
     */
    protected function prepareOrder(OrderInterface $order)
    {
        $date = intval($order->getOrderDate()->getTimestamp());

        $element = [
            'o_id' => $order->getId(),
            //'orderState' => \CoreShop\Model\Order\State::getOrderCurrentState($order), //TODO: Order States
            'orderDate' => $date,
            'orderNumber' => $order->getOrderNumber(),
            'lang' => $order->getOrderLanguage(),
            'carrier' => $order->getCarrier() instanceof CarrierInterface ? $order->getCarrier()->getId() : null,
            'discount' => $order->getDiscount(),
            'subtotal' => $order->getSubtotal(),
            'shipping' => $order->getShipping(),
            'paymentFee' => $order->getPaymentFee(),
            'totalTax' => $order->getTotalTax(),
            'total' => $order->getTotal(),
            'currency' => $this->getCurrency($order->getCurrency() ? $order->getCurrency() : $this->get('coreshop.context.currency')->getCurrency()),
            'currencyName' => $order->getCurrency() instanceof CurrencyInterface ? $order->getCurrency()->getName() : '',
            'shop' => $order->getStore() instanceof StoreInterface ? $order->getStore()->getId() : null,
            'customerName' => $order->getCustomer() instanceof CustomerInterface ? $order->getCustomer()->getFirstname() . ' ' . $order->getCustomer()->getLastname() : '',
            'customerEmail' => $order->getCustomer() instanceof CustomerInterface ? $order->getCustomer()->getEmail() : ''
        ];

        $element = array_merge($element, $this->prepareAddress($order->getShippingAddress(), 'shipping'), $this->prepareAddress($order->getInvoiceAddress(), 'invocie'));

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
        $classDefinition = ClassDefinition::getById($this->getParameter('coreshop.model.address.pimcore_class_id'));

        foreach($classDefinition->getFieldDefinitions() as $fieldDefinition) {
            $value = "";

            if ($address instanceof AddressInterface && $address instanceof Concrete) {
                $value = $address->getValueForFieldName($fieldDefinition->getName());

                if($value instanceof ResourceInterface) {
                    $value = $value->getName();
                }

                $fullAddress[] = $value;
            }

            $values[$prefix . ucfirst($fieldDefinition->getName())] = $value;
        }

        if ($address instanceof AddressInterface && $address->getCountry() instanceof CountryInterface) {
            $values[$prefix . "All"] = $this->getAddressFormatter()->formatAddress($address, false);
        }

        return $values;
    }

    public function detailAction(Request $request)
    {
        $orderId = $request->get('id');
        $order = $this->getOrderRepository()->find($orderId );

        if (!$order instanceof OrderInterface) {
            return  $this->json(['success' => false, 'message' => "Order with ID '$orderId' not found"]);
        }

        $jsonOrder = $this->getDataForObject($order);

        if ($jsonOrder['items'] === null) {
            $jsonOrder['items'] = [];
        }

        $jsonOrder['o_id'] = $order->getId();
        $jsonOrder['customer'] = $order->getCustomer() instanceof PimcoreModelInterface ? $this->getDataForObject($order->getCustomer()) : null;
        //$jsonOrder['statesHistory'] = $this->getStatesHistory($order);
        //$jsonOrder['invoice'] = $order->getProperty('invoice');
        $jsonOrder['invoices'] = []; //$this->getInvoices($order); TODO: invoices
        $jsonOrder['shipments'] = []; //$this->getShipments($order); TODO: Shipments
        $jsonOrder['mailCorrespondence'] = []; //$this->getMailCorrespondence($order); TODO: Mail Correspondence
        $jsonOrder['payments'] = $this->getPayments($order);
        $jsonOrder['editable'] = false; //TODO: count($order->getInvoices()) > 0 ? false : true;
        $jsonOrder['totalPayed'] = 0; //TODO: $order->getPayedTotal();
        $jsonOrder['details'] = $this->getDetails($order);
        $jsonOrder['summary'] = $this->getSummary($order);
        $jsonOrder['currency'] = $this->getCurrency($order->getCurrency() ? $order->getCurrency() : $this->get('coreshop.context.currency')->getCurrency());
        $jsonOrder['shop'] = $order->getStore() instanceof StoreInterface ? $order->getStore() : null;
        //TODO: $jsonOrder['visitor'] = \CoreShop\Model\Visitor::getById($order->getVisitorId());
        $jsonOrder['invoiceCreationAllowed'] = false; //TODO: !$order->isFullyInvoiced() && $order->hasPayments();
        $jsonOrder['shipmentCreationAllowed'] = false; //TODO: !$order->isFullyShipped() && $order->hasPayments();

        $jsonOrder['address'] = [
            'shipping' => $this->getDataForObject($order->getShippingAddress()),
            'billing' => $this->getDataForObject($order->getInvoiceAddress()),
        ];

        if($order->getShippingAddress() instanceof AddressInterface && $order->getShippingAddress()->getCountry() instanceof CountryInterface) {
            $jsonOrder['address']['shipping']['formatted'] = $this->getAddressFormatter()->formatAddress($order->getShippingAddress());
        }
        else {
            $jsonOrder['address']['shipping']['formatted'] = '';
        }

        if($order->getInvoiceAddress() instanceof AddressInterface && $order->getInvoiceAddress()->getCountry() instanceof CountryInterface) {
            $jsonOrder['address']['billing']['formatted'] = $this->getAddressFormatter()->formatAddress($order->getInvoiceAddress());
        }
        else {
            $jsonOrder['address']['billing']['formatted'] = '';
        }

        $jsonOrder['shippingPayment'] = [
            'carrier' => $order->getCarrier() instanceof CarrierInterface ? $order->getCarrier()->getName() : null,
            'weight' => 0, //TODO: $order->getTotalWeight(),
            'cost' => $order->getShipping(),
            //TODO: is this still necessary? I mean, a Order could have multiple different providers
            //'payment' => $order->getPaymentProvider(),
            //'paymentToken' => $order->getPaymentProviderToken(),
            //'paymentDescription' => $order->getPaymentProviderDescription()
        ];

        $jsonOrder['priceRule'] = false;

        /*if ($order->getPriceRuleFieldCollection() instanceof Object\Fieldcollection) {
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
        }*/

        /*$jsonOrder['threads'] = [];
        $jsonOrder['unreadMessages'] = 0;

        $threads = \CoreShop\Model\Messaging\Thread::getList();
        $threads->setCondition("orderId = ?", [$order->getId()]);
        $threads->load();

        foreach ($threads as $thread) {
            if ($thread instanceof \CoreShop\Model\Messaging\Thread) {
                $threadResult = $thread->getObjectVars();

                $messageList = \CoreShop\Model\Messaging\Message::getList();
                $messageList->setCondition("threadId = ? AND `read` = '0'", [$thread->getId()]);
                $messageList->load();

                $threadResult['unread'] = count($messageList->getData());
                $jsonOrder['unreadMessages'] += $threadResult['unread'];
                $jsonOrder['threads'][] = $threadResult;
            }
        }*/


        return $this->json(["success" => true, "order" => $jsonOrder]);
    }

    /**
     * @param OrderInterface $order
     * @return array
     */
    protected function getDetails(OrderInterface $order)
    {
        $details = $order->getItems();
        $items = [];

        foreach ($details as $detail) {
            if ($detail instanceof OrderItemInterface) {
                $items[] = [
                    'o_id' => $detail->getId(),
                    'product' => $detail->getProduct() instanceof ProductInterface ? $detail->getProduct()->getId() : null,
                    'product_name' => $detail->getProduct()->getName(),
                    'product_image' => null, //TODO: ($detail->getProductImage() instanceof \Pimcore\Model\Asset\Image) ? $detail->getProductImage()->getPath() : null,
                    'wholesale_price' => $detail->getItemWholesalePrice(),
                    'price_without_tax' => $detail->getItemPrice(false),
                    'price' => $detail->getItemPrice(true),
                    'amount' => $detail->getQuantity(),
                    'total' => $detail->getTotal(),
                    'total_tax' => $detail->getTotalTax()
                ];
            }
        }

        return $items;
    }

    /**
     * @param OrderInterface $order
     * @return array
     */
    protected function getSummary(OrderInterface $order)
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

        /*TODO: $taxes = $order->getTaxes();

        if (is_array($taxes)) {
            foreach ($taxes as $tax) {
                if ($tax instanceof \CoreShop\Model\Order\Tax) {
                    $summary[] = [
                        'key' => 'tax_' . $tax->getName(),
                        'text' => sprintf($this->view->translateAdmin('Tax (%s - %s)'), $tax->getName(), \CoreShop::getTools()->formatTax($tax->getRate())),
                        'value' => $tax->getAmount()
                    ];
                }
            }
        }*/

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
     * @param OrderInterface $order
     * @return array
     */
    protected function getPayments(OrderInterface $order)
    {
        $payments = $order->getPayments();
        $return = [];

        foreach ($payments as $payment) {
            //TODO: Whatever this was for
            /*$noteList = new \Pimcore\Model\Element\Note\Listing();
            $noteList->addConditionParam('type = ?', \CoreShop\Model\Order\Payment::NOTE_TRANSACTION);
            $noteList->addConditionParam('cid = ?', $payment->getId());
            $noteList->setOrderKey('date');
            $noteList->setOrder('desc');*/

            $return[] = [
                'id' => $payment->getId(),
                'datePayment' => $payment->getDatePayment() ? $payment->getDatePayment()->getTimestamp() : '',
                'provider' => $payment->getPaymentProvider()->getName(),
                'transactionIdentifier' => $payment->getNumber(),
                //'transactionNotes' => $noteList->load(),
                'amount' => $payment->getTotalAmount()
            ];
        }

        return $return;
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
     * @param CurrencyInterface $currency
     * @return array
     */
    protected function getCurrency(CurrencyInterface $currency)
    {
        return [
            'name' => $currency->getName(),
            'symbol' => $currency->getSymbol()
        ];
    }

    private function getOrderRepository() {
        return $this->get('coreshop.repository.order');
    }

    private function getOrderList() {
        return $this->getOrderRepository()->getListingClass();
    }

    private function getAddressFormatter() {
        return $this->get('coreshop.address.formatter');
    }
}
