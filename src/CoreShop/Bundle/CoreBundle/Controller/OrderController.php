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

use Carbon\Carbon;
use CoreShop\Bundle\ResourceBundle\Controller\AdminController;
use CoreShop\Component\Address\Formatter\AddressFormatterInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Core\Model\PaymentProviderInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Order\Processable\ProcessableInterface;
use CoreShop\Component\Order\Repository\OrderInvoiceRepositoryInterface;
use CoreShop\Component\Order\Repository\OrderShipmentRepositoryInterface;
use CoreShop\Component\Order\Workflow\WorkflowManagerInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Payment\Repository\PaymentRepositoryInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Pimcore\Model\Object;
use Pimcore\Model\Object\ClassDefinition;
use Pimcore\Model\Object\Concrete;
use Symfony\Component\HttpFoundation\Request;

/**
 * @todo: maybe we should move this one to the AdminBundle?
 */
class OrderController extends AdminController
{
    /**
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function getOrderGridConfigurationAction()
    {
        $defaultConfiguration = [
            [
                'text' => 'coreshop_store',
                'type' => 'integer',
                'dataIndex' => 'store',
                'renderAs' => 'store',
                'filter' => [
                    'type' => 'number',
                ],
            ],
            [
                'text' => 'coreshop_orders_id',
                'type' => 'string',
                'dataIndex' => 'o_id',
                'filter' => [
                    'type' => 'number',
                ],
                'hideable' => false,
                'draggable' => false,
            ],
            [
                'text' => 'coreshop_orders_orderNumber',
                'type' => 'string',
                'dataIndex' => 'orderNumber',
                'filter' => [
                    'type' => 'string',
                ],
            ],
            [
                'text' => 'name',
                'type' => 'string',
                'dataIndex' => 'customerName',
                'flex' => 1,
            ],
            [
                'text' => 'email',
                'type' => 'string',
                'dataIndex' => 'customerEmail',
                'width' => 200,
            ],
            [
                'text' => 'coreshop_orders_total',
                'type' => 'float',
                'dataIndex' => 'total',
                'renderAs' => 'currency',
                'filter' => [
                    'type' => 'number',
                ],
                'align' => 'right',
            ],
            [
                'text' => 'coreshop_discount',
                'type' => 'float',
                'dataIndex' => 'discount',
                'renderAs' => 'currency',
                'align' => 'right',
                'hidden' => true,
            ],
            [
                'text' => 'coreshop_subtotal',
                'type' => 'float',
                'dataIndex' => 'subtotal',
                'renderAs' => 'currency',
                'align' => 'right',
                'hidden' => true,
            ],
            [
                'text' => 'coreshop_shipping',
                'type' => 'float',
                'dataIndex' => 'shipping',
                'renderAs' => 'currency',
                'align' => 'right',
                'hidden' => true,
            ],
            [
                'text' => 'coreshop_paymentFee',
                'type' => 'float',
                'dataIndex' => 'paymentFee',
                'renderAs' => 'currency',
                'align' => 'right',
                'hidden' => true,
            ],
            [
                'text' => 'coreshop_total_tax',
                'type' => 'float',
                'dataIndex' => 'totalTax',
                'renderAs' => 'currency',
                'align' => 'right',
                'hidden' => true,
            ],
            [
                'text' => 'coreshop_currency',
                'type' => 'string',
                'dataIndex' => 'currencyName',
                'align' => 'right',
                'hidden' => true,
            ],
            [
                'text' => 'coreshop_orders_orderState',
                'type' => null,
                'dataIndex' => 'orderState',
                'renderAs' => 'orderState',
                'width' => 200,
            ],
            [
                'text' => 'coreshop_orders_orderDate',
                'type' => 'date',
                'dataIndex' => 'orderDate',
                'filter' => [
                    'type' => 'date',
                ],
                'width' => 150,
            ],
        ];

        $addressClassId = $this->getParameter('coreshop.model.address.pimcore_class_id');

        $addressClassDefinition = \Pimcore\Model\Object\ClassDefinition::getById($addressClassId);
        $addressFields = [];

        if ($addressClassDefinition instanceof \Pimcore\Model\Object\ClassDefinition) {
            $invalidFields = ['extra'];

            foreach ($addressClassDefinition->getFieldDefinitions() as $fieldDefinition) {
                if (in_array($fieldDefinition->getName(), $invalidFields)) {
                    continue;
                }

                $niceName = ucwords(str_replace('_', ' ', $fieldDefinition->getName()));

                $addressFields[] = [
                    'fieldName' => $niceName,
                    'type' => 'string',
                    'dataIndex' => $fieldDefinition->getName(),
                    'width' => 150,
                    'hidden' => true,
                ];
            }

            $addressFields[] = [
                'fieldName' => 'All',
                'type' => 'string',
                'dataIndex' => 'All',
                'width' => 150,
                'hidden' => true,
            ];
        }

        foreach (['shipping', 'invoice'] as $type) {
            foreach ($addressFields as $fieldElement) {
                $name = $fieldElement['fieldName'];
                $dataIndex = $fieldElement['dataIndex'];

                $fieldElement['text'] = 'coreshop_address_'.$type.'|['.$name.']';
                $fieldElement['dataIndex'] = 'address'.ucfirst($type).ucfirst($dataIndex);

                $defaultConfiguration[] = $fieldElement;
            }
        }

        return $this->json(['success' => true, 'columns' => $defaultConfiguration]);
    }

    public function getOrders(Request $request)
    {
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
     *
     * @return array
     */
    protected function prepareOrder(OrderInterface $order)
    {
        $date = intval($order->getOrderDate()->getTimestamp());

        $element = [
            'o_id' => $order->getId(),
            'orderState' => $this->getOrderStateManager()->getCurrentState($order),
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
            'customerName' => $order->getCustomer() instanceof CustomerInterface ? $order->getCustomer()->getFirstname().' '.$order->getCustomer()->getLastname() : '',
            'customerEmail' => $order->getCustomer() instanceof CustomerInterface ? $order->getCustomer()->getEmail() : '',
        ];

        $element = array_merge($element, $this->prepareAddress($order->getShippingAddress(), 'shipping'), $this->prepareAddress($order->getInvoiceAddress(), 'invocie'));

        return $element;
    }

    /**
     * @param $address
     * @param $type
     *
     * @return array
     */
    protected function prepareAddress($address, $type)
    {
        $prefix = 'address'.ucfirst($type);
        $values = [];
        $fullAddress = [];
        $classDefinition = ClassDefinition::getById($this->getParameter('coreshop.model.address.pimcore_class_id'));

        foreach ($classDefinition->getFieldDefinitions() as $fieldDefinition) {
            $value = '';

            if ($address instanceof AddressInterface && $address instanceof Concrete) {
                $getter = "get" . ucfirst($fieldDefinition->getName());

                if (method_exists($address, $getter)) {
                    $value = $address->$getter();

                    if ($value instanceof ResourceInterface) {
                        $value = $value->getName();
                    }

                    $fullAddress[] = $value;
                }
            }

            $values[$prefix.ucfirst($fieldDefinition->getName())] = $value;
        }

        if ($address instanceof AddressInterface && $address->getCountry() instanceof CountryInterface) {
            $values[$prefix.'All'] = $this->getAddressFormatter()->formatAddress($address, false);
        }

        return $values;
    }

    public function detailAction(Request $request)
    {
        $orderId = $request->get('id');
        $order = $this->getOrderRepository()->find($orderId);

        if (!$order instanceof OrderInterface) {
            return  $this->json(['success' => false, 'message' => "Order with ID '$orderId' not found"]);
        }

        $jsonOrder = $this->getDataForObject($order);

        if ($jsonOrder['items'] === null) {
            $jsonOrder['items'] = [];
        }

        $jsonOrder['o_id'] = $order->getId();
        $jsonOrder['customer'] = $order->getCustomer() instanceof PimcoreModelInterface ? $this->getDataForObject($order->getCustomer()) : null;
        $jsonOrder['statesHistory'] = $this->getStatesHistory($order);
        $jsonOrder['invoices'] = $this->getInvoices($order);
        $jsonOrder['shipments'] = $this->getShipments($order);
        $jsonOrder['mailCorrespondence'] = []; //$this->getMailCorrespondence($order); TODO: Mail Correspondence
        $jsonOrder['payments'] = $this->getPayments($order);
        $jsonOrder['editable'] = count($this->getInvoices($order)) > 0 ? false : true;
        $jsonOrder['totalPayed'] = $order->getTotalPayed();
        $jsonOrder['details'] = $this->getDetails($order);
        $jsonOrder['summary'] = $this->getSummary($order);
        $jsonOrder['currency'] = $this->getCurrency($order->getCurrency() ? $order->getCurrency() : $this->get('coreshop.context.currency')->getCurrency());
        $jsonOrder['shop'] = $order->getStore() instanceof StoreInterface ? $order->getStore() : null;
        //TODO: $jsonOrder['visitor'] = \CoreShop\Model\Visitor::getById($order->getVisitorId());
        $jsonOrder['invoiceCreationAllowed'] = !$this->getInvoiceProcessableHelper()->isFullyProcessed($order) && count($order->getPayments()) !== 0;
        $jsonOrder['shipmentCreationAllowed'] = !$this->getShipmentProcessableHelper()->isFullyProcessed($order) && count($order->getPayments()) !== 0;

        $jsonOrder['address'] = [
            'shipping' => $this->getDataForObject($order->getShippingAddress()),
            'billing' => $this->getDataForObject($order->getInvoiceAddress()),
        ];

        if ($order->getShippingAddress() instanceof AddressInterface && $order->getShippingAddress()->getCountry() instanceof CountryInterface) {
            $jsonOrder['address']['shipping']['formatted'] = $this->getAddressFormatter()->formatAddress($order->getShippingAddress());
        } else {
            $jsonOrder['address']['shipping']['formatted'] = '';
        }

        if ($order->getInvoiceAddress() instanceof AddressInterface && $order->getInvoiceAddress()->getCountry() instanceof CountryInterface) {
            $jsonOrder['address']['billing']['formatted'] = $this->getAddressFormatter()->formatAddress($order->getInvoiceAddress());
        } else {
            $jsonOrder['address']['billing']['formatted'] = '';
        }

        $jsonOrder['shippingPayment'] = [
            'carrier' => $order->getCarrier() instanceof CarrierInterface ? $order->getCarrier()->getName() : null,
            'weight' => $order->getWeight(),
            'cost' => $order->getShipping(),
        ];

        $jsonOrder['priceRule'] = false;

        if ($order->getPriceRuleItems() instanceof Object\Fieldcollection) {
            $rules = [];

            foreach ($order->getPriceRuleItems()->getItems() as $ruleItem) {
                if ($ruleItem instanceof ProposalCartPriceRuleItemInterface) {
                    $rule = $ruleItem->getCartPriceRule();

                    if ($rule instanceof CartPriceRuleInterface) {
                        $rules[] = [
                            'id' => $rule->getId(),
                            'name' => $rule->getName(),
                            'code' => $ruleItem->getVoucherCode(),
                            'discount' => $ruleItem->getDiscount(),
                        ];
                    }
                }
            }

            $jsonOrder['priceRule'] = $rules;
        }

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

        return $this->json(['success' => true, 'order' => $jsonOrder]);
    }

    /**
     * @param Request $request
     *
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function updatePaymentAction(Request $request)
    {
        $payment = $this->getPaymentRepository()->find($request->get('id'));

        if (!$payment instanceof PaymentInterface) {
            return $this->json(['success' => false]);
        }

        $payment->setValues($request->request->all());

        $this->getEntityManager()->persist($payment);
        $this->getEntityManager()->flush();

        return $this->json(['success' => true]);
    }

    /**
     * @param Request $request
     *
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function addPaymentAction(Request $request)
    {
        $orderId = $request->get('o_id');
        $order = $this->getOrderRepository()->find($orderId);
        $amount = doubleval($request->get('amount', 0));
        $transactionId = $request->get('transactionNumber');
        $paymentProviderId = $request->get('paymentProvider');

        if (!$order instanceof OrderInterface) {
            return $this->json(['success' => false, 'message' => 'Order with ID "'.$orderId.'" not found']);
        }

        $paymentProvider = $this->getPaymentRepository()->find($paymentProviderId);

        if ($paymentProvider instanceof PaymentProviderInterface) {
            $payedTotal = $order->getTotalPayed();

            $payedTotal += $amount;

            if ($payedTotal > $order->getTotal()) {
                return $this->json(['success' => false, 'message' => 'Payed Amount is greater than order amount']);
            } else {
                /**
                 * @var PaymentInterface|PimcoreModelInterface
                 */
                $payment = $this->getPaymentFactory()->createNew();
                $payment->setNumber($transactionId);
                $payment->setPaymentProvider($paymentProvider);
                $payment->setCurrency($order->getCurrency());
                $payment->setTotalAmount($order->getTotal());
                $payment->setState(PaymentInterface::STATE_NEW);
                $payment->setDatePayment(Carbon::now());
                $payment->setOrderId($order->getId());

                $this->getEntityManager()->persist($payment);
                $this->getEntityManager()->flush();

                return $this->json(['success' => true, 'payments' => $this->getPayments($order), 'totalPayed' => $order->getTotalPayed()]);
            }
        } else {
            return $this->json(['success' => false, 'message' => "Payment Provider '$paymentProvider' not found"]);
        }
    }

    /**
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function getPaymentProvidersAction()
    {
        $providers = $this->getPaymentRepository()->findAll();
        $result = [];

        foreach ($providers as $provider) {
            if ($provider instanceof PaymentProviderInterface) {
                $result[] = [
                    'name' => $provider->getName(),
                    'id' => $provider->getId(),
                ];
            }
        }

        return $this->json(['success' => true, 'data' => $result]);
    }

    /**
     * @param OrderInterface $order
     *
     * @return array
     */
    protected function getStatesHistory(OrderInterface $order)
    {
        //Get History
        $manager = $this->getOrderStateManager();
        $history = $manager->getStateHistory($order);

        // create timeline
        $statesHistory = [];

        $date = Carbon::now();

        if (is_array($history)) {
            foreach ($history as $note) {
                $user = \Pimcore\Model\User::getById($note->getUser());
                $avatar = $user ? sprintf('/admin/user/get-image?id=%d', $user->getId()) : null;

                $statesHistory[] = [
                    'icon' => 'coreshop_icon_orderstates',
                    'type' => $note->getType(),
                    'date' => $date->formatLocalized('%A %d %B %Y'),
                    'avatar' => $avatar,
                    'user' => $user ? $user->getName() : null,
                    'description' => $note->getDescription(),
                    'title' => $note->getTitle(),
                    'data' => $note->getData(),
                ];
            }
        }

        return $statesHistory;
    }

    /**
     * @param OrderInterface $order
     *
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
                    'total_tax' => $detail->getTotalTax(),
                ];
            }
        }

        return $items;
    }

    /**
     * @param OrderInterface $order
     *
     * @return array
     */
    protected function getSummary(OrderInterface $order)
    {
        $summary = [];

        if ($order->getDiscount() > 0) {
            $summary[] = [
                'key' => 'discount',
                'value' => $order->getDiscount(),
            ];
        }

        if ($order->getShipping() > 0) {
            $summary[] = [
                'key' => 'shipping',
                'value' => $order->getShipping(),
            ];

            $summary[] = [
                'key' => 'shipping_tax',
                'value' => $order->getShippingTax(),
            ];
        }

        if ($order->getPaymentFee() > 0) {
            $summary[] = [
                'key' => 'payment',
                'value' => $order->getPaymentFee(),
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
            'value' => $order->getTotalTax(),
        ];
        $summary[] = [
            'key' => 'total',
            'value' => $order->getTotal(),
        ];

        return $summary;
    }

    /**
     * @param OrderInterface $order
     *
     * @return array
     */
    protected function getPayments(OrderInterface $order)
    {
        $payments = $order->getPayments();
        $return = [];

        foreach ($payments as $payment) {
            //TODO: Whatever this was for
            //TODO: Actually, this was not bad at all, it saved history for payments, but needs to be different now
            //TODO: Payment Model has a detail array, where it can store any info
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
                'amount' => $payment->getTotalAmount(),
                'state' => $payment->getState(),
            ];
        }

        return $return;
    }

    /**
     * @param Object\Concrete $data
     *
     * @return array
     */
    private function getDataForObject(Object\Concrete $data)
    {
        $objectData = [];
        Object\Service::loadAllObjectFields($data);

        foreach ($data->getClass()->getFieldDefinitions() as $key => $def) {
            $getter = 'get'.ucfirst($key);
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
     * @param OrderInterface $order
     *
     * @return array
     */
    protected function getInvoices($order)
    {
        $invoices = $this->getOrderInvoiceRepository()->getDocuments($order);
        $invoiceArray = [];

        foreach ($invoices as $invoice) {
            $invoiceArray[] = $this->getDataForObject($invoice);
        }

        return $invoiceArray;
    }

    /**
     * @param OrderInterface $order
     *
     * @return array
     */
    protected function getShipments($order)
    {
        $invoices = $this->getOrderShipmentRepository()->getDocuments($order);
        $invoiceArray = [];

        foreach ($invoices as $invoice) {
            $invoiceArray[] = $this->getDataForObject($invoice);
        }

        return $invoiceArray;
    }

    /**
     * @return ProcessableInterface
     */
    private function getInvoiceProcessableHelper()
    {
        return $this->get('coreshop.order.invoice.processable');
    }

    /**
     * @return ProcessableInterface
     */
    private function getShipmentProcessableHelper()
    {
        return $this->get('coreshop.order.shipment.processable');
    }

    /**
     * @return PimcoreRepositoryInterface
     */
    private function getOrderRepository()
    {
        return $this->get('coreshop.repository.order');
    }

    /**
     * @return OrderInvoiceRepositoryInterface
     */
    private function getOrderInvoiceRepository()
    {
        return $this->get('coreshop.repository.order_invoice');
    }

    /**
     * @return OrderShipmentRepositoryInterface
     */
    private function getOrderShipmentRepository()
    {
        return $this->get('coreshop.repository.order_shipment');
    }

    /**
     * @return \Pimcore\Model\Listing\AbstractListing
     */
    private function getOrderList()
    {
        return $this->getOrderRepository()->getList();
    }

    /**
     * @return AddressFormatterInterface
     */
    private function getAddressFormatter()
    {
        return $this->get('coreshop.address.formatter');
    }

    /**
     * @return WorkflowManagerInterface
     */
    private function getOrderStateManager()
    {
        return $this->get('coreshop.workflow.manager.order');
    }

    /**
     * @return RepositoryInterface
     */
    private function getPaymentRepository()
    {
        return $this->get('coreshop.repository.payment');
    }

    /**
     * @return \Doctrine\ORM\EntityManager|object
     */
    private function getEntityManager()
    {
        return $this->get('doctrine.orm.entity_manager');
    }

    /**
     * @return PaymentRepositoryInterface
     */
    private function getPaymentProviderRepository()
    {
        return $this->get('coreshop.repository.payment_provider');
    }

    /**
     * @return FactoryInterface
     */
    private function getPaymentFactory()
    {
        return $this->get('coreshop.factory.payment');
    }
}
