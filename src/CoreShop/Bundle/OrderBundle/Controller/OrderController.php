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

declare(strict_types=1);

namespace CoreShop\Bundle\OrderBundle\Controller;

use Carbon\Carbon;
use CoreShop\Bundle\OrderBundle\Events;
use CoreShop\Bundle\ResourceBundle\Controller\PimcoreController;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManagerInterface;
use CoreShop\Bundle\WorkflowBundle\History\HistoryLogger;
use CoreShop\Bundle\WorkflowBundle\StateManager\WorkflowStateInfoManagerInterface;
use CoreShop\Component\Address\Formatter\AddressFormatterInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Address\Model\CountryInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Order\Notes;
use CoreShop\Component\Order\OrderStates;
use CoreShop\Component\Order\OrderTransitions;
use CoreShop\Component\Order\Processable\ProcessableInterface;
use CoreShop\Component\Order\Repository\OrderInvoiceRepositoryInterface;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\Order\Repository\OrderShipmentRepositoryInterface;
use CoreShop\Component\Payment\Repository\PaymentRepositoryInterface;
use CoreShop\Component\Pimcore\DataObject\DataLoader;
use CoreShop\Component\Pimcore\DataObject\NoteServiceInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;
use JMS\Serializer\ArrayTransformerInterface;
use Pimcore\Bundle\AdminBundle\Helper\GridHelperService;
use Pimcore\Bundle\AdminBundle\Helper\QueryParams;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Listing;
use Pimcore\Model\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Workflow\StateMachine;

class OrderController extends PimcoreController
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var NoteServiceInterface
     */
    protected $objectNoteService;
    /**
     * @var AddressFormatterInterface
     */
    protected $addressFormatter;

    /**
     * @var ArrayTransformerInterface
     */
    protected $serializer;

    /**
     * @var WorkflowStateInfoManagerInterface
     */
    protected $workflowStateManager;

    /**
     * @var ProcessableInterface
     */
    protected $invoiceProcessableHelper;

    /**
     * @var ProcessableInterface
     */
    protected $shipmentProcessableHelper;

    /**
     * @var OrderInvoiceRepositoryInterface
     */
    protected $orderInvoiceRepository;

    /**
     * @var OrderShipmentRepositoryInterface
     */
    protected $orderShipmentRepository;

    /**
     * @var PaymentRepositoryInterface
     */
    protected $paymentRepository;

    public function getStatesAction(Request $request): Response
    {
        $identifiers = $this->getParameter('coreshop.state_machines');
        $states = [];
        $transitions = [];

        foreach ($identifiers as $identifier) {
            $transitions[$identifier] = [];
            $states[$identifier] = [];

            /**
             * @var StateMachine $stateMachine
             */
            $stateMachine = $this->get(sprintf('state_machine.%s', $identifier));
            $places = $stateMachine->getDefinition()->getPlaces();
            $machineTransitions = $stateMachine->getDefinition()->getTransitions();

            foreach ($places as $place) {
                $states[$identifier][] = $this->workflowStateManager->getStateInfo($identifier, $place, false);
            }

            foreach ($machineTransitions as $transition) {
                if (!array_key_exists($transition->getName(), $transitions[$identifier])) {
                    $transitions[$identifier][$transition->getName()] = [
                        'name' => $transition->getName(),
                        'froms' => [],
                        'tos' => [],
                    ];
                }

                $transitions[$identifier][$transition->getName()]['froms'] =
                    array_merge(
                        $transitions[$identifier][$transition->getName()]['froms'],
                        $transition->getFroms()
                    );

                $transitions[$identifier][$transition->getName()]['tos'] =
                    array_merge(
                        $transitions[$identifier][$transition->getName()]['tos'],
                        $transition->getFroms()
                    );
            }

            $transitions[$identifier] = array_values($transitions[$identifier]);
        }

        return $this->viewHandler->handle(['success' => true, 'states' => $states, 'transitions' => $transitions]);
    }

    public function updateOrderStateAction(
        Request $request,
        OrderRepositoryInterface $orderRepository,
        StateMachineManagerInterface $stateMachineManager
    ): Response
    {
        $orderId = $request->get('o_id');
        $order = $orderRepository->find($orderId);
        $transition = $request->get('transition');

        if (!$order instanceof OrderInterface) {
            throw new \Exception('invalid order');
        }

        //apply state machine
        $workflow = $stateMachineManager->get($order, 'coreshop_order');
        if (!$workflow->can($order, $transition)) {
            return $this->viewHandler->handle(['success' => false, 'message' => 'this transition is not allowed.']);
        }

        $workflow->apply($order, $transition);

        if ($order instanceof DataObject\Concrete && $transition === OrderTransitions::TRANSITION_CANCEL) {
            $this->get(HistoryLogger::class)->log(
                $order,
                'Admin Order Cancellation'
            );
        }

        return $this->viewHandler->handle(['success' => true]);
    }

    public function getFolderConfigurationAction(Request $request): Response
    {
        $this->isGrantedOr403();

        $name = null;
        $folderId = null;

        $type = $request->get('saleType', 'order');

        $orderClassId = $this->getParameter('coreshop.model.order.pimcore_class_name');
        $folderPath = $this->getParameter('coreshop.folder.' . $type);
        $orderClassDefinition = DataObject\ClassDefinition::getByName($orderClassId);

        $folder = DataObject::getByPath('/' . $folderPath);

        if ($folder instanceof DataObject\Folder) {
            $folderId = $folder->getId();
        }

        if ($orderClassDefinition instanceof DataObject\ClassDefinition) {
            $name = $orderClassDefinition->getName();
        }

        return $this->viewHandler->handle(['success' => true, 'className' => $name, 'folderId' => $folderId]);
    }

    public function listAction(Request $request, OrderRepositoryInterface $orderRepository): Response
    {
        $this->isGrantedOr403();

        $list = $orderRepository->getList();
        $list->setLimit($request->get('limit', 30));
        $list->setOffset($request->get('page', 1) - 1);

        if ($request->get('filter', null)) {
            $gridHelper = new GridHelperService();

            $conditionFilters = [];
            $conditionFilters[] = $gridHelper->getFilterCondition($request->get('filter'),
                DataObject\ClassDefinition::getByName($this->getParameter('coreshop.model.order.pimcore_class_name')));
            if (count($conditionFilters) > 0 && $conditionFilters[0] !== '(())') {
                $list->setCondition(implode(' AND ', $conditionFilters));
            }
        }

        $sortingSettings = QueryParams::extractSortingSettings($request->request->all());

        $order = 'DESC';
        $orderKey = 'orderDate';

        if ($sortingSettings['order']) {
            $order = $sortingSettings['order'];
        }
        if ($sortingSettings['orderKey'] !== '') {
            $orderKey = $sortingSettings['orderKey'];
        }

        $list->setOrder($order);
        $list->setOrderKey($orderKey);

        /**
         * @var Listing $list
         */
        $orders = $list->getData();
        $jsonSales = [];

        foreach ($orders as $order) {
            $jsonSales[] = $this->prepareSale($order);
        }

        return $this->viewHandler->handle([
            'success' => true,
            'data' => $jsonSales,
            'count' => count($jsonSales),
            'total' => $list->getTotalCount(),
        ]);
    }

    public function detailAction(Request $request, OrderRepositoryInterface $orderRepository): Response
    {
        $this->isGrantedOr403();

        $orderId = $request->get('id');
        $order = $orderRepository->find($orderId);

        if (!$order instanceof OrderInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => "Order with ID '$orderId' not found"]);
        }

        $jsonSale = $this->getDetails($order);

        return $this->viewHandler->handle(['success' => true, 'sale' => $jsonSale]);
    }

    public function findOrderAction(Request $request, OrderRepositoryInterface $orderRepository): Response
    {
        $this->isGrantedOr403();

        $number = $request->get('number');

        if ($number) {
            $list = $orderRepository->getList();
            $list->setCondition('orderNumber = ? OR o_id = ?', [$number, $number]);

            $orders = $list->getData();

            if (count($orders) > 0) {
                return $this->viewHandler->handle(['success' => true, 'id' => $orders[0]->getId()]);
            }
        }

        return $this->viewHandler->handle(['success' => false]);
    }

    protected function prepareSale(OrderInterface $order): array
    {
        $date = (int)$order->getOrderDate()->getTimestamp();

        $element = [
            'o_id' => $order->getId(),
            'saleDate' => $date,
            'saleNumber' => $order->getOrderNumber(),
            'lang' => $order->getLocaleCode(),
            'discount' => $order->getDiscount(),
            'subtotal' => $order->getSubtotal(),
            'totalTax' => $order->getTotalTax(),
            'total' => $order->getTotal(),
            'currency' => $this->getCurrency($order->getCurrency() ?: $order->getStore()->getCurrency()),
            'currencyName' => $order->getCurrency() instanceof CurrencyInterface ? $order->getCurrency()->getName() : '',
            'customerName' => $order->getCustomer() instanceof CustomerInterface ? $order->getCustomer()->getFirstname().' '.$order->getCustomer()->getLastname() : '',
            'customerEmail' => $order->getCustomer() instanceof CustomerInterface ? $order->getCustomer()->getEmail() : '',
            'store' => $order->getStore() instanceof StoreInterface ? $order->getStore()->getId() : null,
            'orderState' => $this->workflowStateManager->getStateInfo('coreshop_order', $order->getOrderState(), false),
            'orderPaymentState' => $this->workflowStateManager->getStateInfo('coreshop_order_payment', $order->getPaymentState(), false),
            'orderShippingState' => $this->workflowStateManager->getStateInfo('coreshop_order_shipment', $order->getShippingState(), false),
            'orderInvoiceState' => $this->workflowStateManager->getStateInfo('coreshop_order_invoice', $order->getInvoiceState(), false)
        ];

        $element = array_merge(
            $element,
            $this->prepareAddress($order->getShippingAddress(), 'shipping'),
            $this->prepareAddress($order->getInvoiceAddress(), 'invoice')
        );

        return $element;
    }

    protected function prepareAddress(AddressInterface $address, string $type): array
    {
        $prefix = 'address'.ucfirst($type);
        $values = [];
        $fullAddress = [];
        $classDefinition = DataObject\ClassDefinition::getByName($this->getParameter('coreshop.model.address.pimcore_class_name'));

        foreach ($classDefinition->getFieldDefinitions() as $fieldDefinition) {
            $value = '';

            if ($address instanceof AddressInterface && $address instanceof DataObject\Concrete) {
                $getter = 'get'.ucfirst($fieldDefinition->getName());

                if (method_exists($address, $getter)) {
                    $value = $address->$getter();

                    if (method_exists($value, 'getName')) {
                        $value = $value->getName();
                    }

                    $fullAddress[] = $value;
                }
            }

            $values[$prefix.ucfirst($fieldDefinition->getName())] = $value;
        }

        if ($address instanceof AddressInterface && $address->getCountry() instanceof \CoreShop\Component\Address\Model\CountryInterface) {
            $values[$prefix.'All'] = $this->addressFormatter->formatAddress($address, false);
        }

        return $values;
    }

    protected function getDetails(OrderInterface $order): array
    {
        $jsonSale = $this->serializer->toArray($order);

        $jsonSale['o_id'] = $order->getId();
        $jsonSale['saleNumber'] = $order->getOrderNumber();
        $jsonSale['saleDate'] = $order->getOrderDate() ? $order->getOrderDate()->getTimestamp() : null;
        $jsonSale['currency'] = $this->getCurrency($order->getCurrency() ?: $order->getStore()->getCurrency());
        $jsonSale['store'] = $order->getStore() instanceof StoreInterface ? $this->getStore($order->getStore()) : null;

        if (!isset($jsonSale['items'])) {
            $jsonSale['items'] = [];
        }

        $jsonSale['details'] = $this->getItemDetails($order);
        $jsonSale['summary'] = $this->getSummary($order);
        $jsonSale['mailCorrespondence'] = $this->getMailCorrespondence($order);

        $jsonSale['address'] = [
            'shipping' => $this->getDataForObject($order->getShippingAddress()),
            'billing' => $this->getDataForObject($order->getInvoiceAddress()),
        ];

        if ($order->getShippingAddress() instanceof AddressInterface && $order->getShippingAddress()->getCountry() instanceof CountryInterface) {
            $jsonSale['address']['shipping']['formatted'] = $this->addressFormatter->formatAddress($order->getShippingAddress());
        } else {
            $jsonSale['address']['shipping']['formatted'] = '';
        }

        if ($order->getInvoiceAddress() instanceof AddressInterface && $order->getInvoiceAddress()->getCountry() instanceof CountryInterface) {
            $jsonSale['address']['billing']['formatted'] = $this->addressFormatter->formatAddress($order->getInvoiceAddress());
        } else {
            $jsonSale['address']['billing']['formatted'] = '';
        }

        $jsonSale['priceRule'] = false;

        if ($order->getPriceRuleItems() instanceof DataObject\Fieldcollection) {
            $rules = [];

            foreach ($order->getPriceRuleItems()->getItems() as $ruleItem) {
                if ($ruleItem instanceof ProposalCartPriceRuleItemInterface) {
                    $rule = $ruleItem->getCartPriceRule();

                    $ruleData = [
                        'id' => -1,
                        'name' => '--',
                        'code' => empty($ruleItem->getVoucherCode()) ? null : $ruleItem->getVoucherCode(),
                        'discount' => $ruleItem->getDiscount(),
                    ];

                    if ($rule instanceof CartPriceRuleInterface) {
                        $ruleData = array_merge($ruleData, [
                            'id' => $rule->getId(),
                            'name' => $rule->getName(),
                        ]);
                    }

                    $rules[] = $ruleData;
                }
            }

            $jsonSale['priceRule'] = $rules;
        }

        $jsonSale['orderState'] = $this->workflowStateManager->getStateInfo('coreshop_order', $order->getOrderState(), false);
        $jsonSale['orderPaymentState'] = $this->workflowStateManager->getStateInfo('coreshop_order_payment', $order->getPaymentState(), false);
        $jsonSale['orderShippingState'] = $this->workflowStateManager->getStateInfo('coreshop_order_shipment', $order->getShippingState(), false);
        $jsonSale['orderInvoiceState'] = $this->workflowStateManager->getStateInfo('coreshop_order_invoice', $order->getInvoiceState(), false);

        $availableTransitions = $this->workflowStateManager->parseTransitions($order, 'coreshop_order', [
            'cancel',
        ], false);

        $jsonSale['availableOrderTransitions'] = $availableTransitions;
        $jsonSale['statesHistory'] = $this->getStatesHistory($order);


        $invoices = $this->getInvoices($order);

        $jsonSale['editable'] = count($invoices) > 0 ? false : true;
        $jsonSale['invoices'] = $invoices;
        $jsonSale['payments'] = $this->getPayments($order);
        $jsonSale['shipments'] = $this->getShipments($order);
        $jsonSale['paymentCreationAllowed'] = !in_array($order->getOrderState(), [OrderStates::STATE_CANCELLED, OrderStates::STATE_COMPLETE]);
        $jsonSale['invoiceCreationAllowed'] = $this->invoiceProcessableHelper->isProcessable($order);
        $jsonSale['shipmentCreationAllowed'] = $this->shipmentProcessableHelper->isProcessable($order);

        $event = new GenericEvent($order, $jsonSale);

        $this->eventDispatcher->dispatch(Events::SALE_DETAIL_PREPARE, $event);

        $jsonSale = $event->getArguments();

        return $jsonSale;
    }

    protected function getMailCorrespondence(OrderInterface $order): array
    {
        $list = [];

        $notes = $this->objectNoteService->getObjectNotes($order, Notes::NOTE_EMAIL);

        foreach ($notes as $note) {
            $noteElement = [
                'date' => $note->date,
                'description' => $note->description,
            ];

            foreach ($note->data as $key => $noteData) {
                $noteElement[$key] = $noteData['data'];
            }

            $list[] = $noteElement;
        }

        return $list;
    }

    protected function getInvoices(OrderInterface $order): array
    {
        $invoices = $this->orderInvoiceRepository->getDocuments($order);
        $invoiceArray = [];

        foreach ($invoices as $invoice) {
            $availableTransitions = $this->workflowStateManager->parseTransitions($invoice, 'coreshop_invoice', [
                'complete',
                'cancel',
            ], false);

            $data = $this->serializer->toArray($invoice);

            $data['stateInfo'] = $this->workflowStateManager->getStateInfo('coreshop_invoice', $invoice->getState(), false);
            $data['transitions'] = $availableTransitions;

            $invoiceArray[] = $data;
        }

        return $invoiceArray;
    }

    protected function getShipments($order)
    {
        $shipments = $this->orderShipmentRepository->getDocuments($order);
        $shipmentArray = [];

        foreach ($shipments as $shipment) {

            $availableTransitions = $this->workflowStateManager->parseTransitions($shipment, 'coreshop_shipment', [
                'create',
                'ship',
                'cancel',
            ], false);

            $data = $this->serializer->toArray($shipment);

            $data['stateInfo'] = $this->workflowStateManager->getStateInfo('coreshop_shipment', $shipment->getState(), false);
            $data['transitions'] = $availableTransitions;

            $shipmentArray[] = $data;
        }

        return $shipmentArray;
    }

    protected function getSummary(OrderInterface $order): array
    {
        $summary = [];

        if ($order->getDiscount() != 0) {
            $summary[] = [
                'key' => $order->getDiscount() < 0 ? 'discount' : 'surcharge',
                'value' => $order->getDiscount(),
            ];
        }

        $taxes = $order->getTaxes();

        if (is_array($taxes)) {
            foreach ($taxes as $tax) {
                if ($tax instanceof TaxItemInterface) {
                    $summary[] = [
                        'key' => 'tax_'.$tax->getName(),
                        'text' => sprintf('Tax (%s - %s)', $tax->getName(), $tax->getRate()),
                        'value' => $tax->getAmount(),
                    ];
                }
            }
        }

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

    protected function getItemDetails(OrderInterface $order): array
    {
        $details = $order->getItems();
        $items = [];

        foreach ($details as $detail) {
            if ($detail instanceof OrderItemInterface) {
                $items[] = $this->prepareSaleItem($detail);
            }
        }

        return $items;
    }

    protected function prepareSaleItem(OrderItemInterface $item): array
    {
        return [
            'o_id' => $item->getId(),
            'product_name' => $item->getName(),
            'product_image' => null,
            //TODO: ($detail->getProductImage() instanceof \Pimcore\Model\Asset\Image) ? $detail->getProductImage()->getPath() : null,
            'wholesale_price' => $item->getItemWholesalePrice(),
            'price_without_tax' => $item->getItemPrice(false),
            'price' => $item->getItemPrice(true),
            'quantity' => $item->getQuantity(),
            'total' => $item->getTotal(),
            'total_tax' => $item->getTotalTax(),
        ];
    }

    protected function getStatesHistory(OrderInterface $order)
    {
        //Get History
        $history = $this->workflowStateManager->getStateHistory($order);

        $statesHistory = [];

        if (is_array($history)) {
            foreach ($history as $note) {
                $user = User::getById($note->getUser());
                $avatar = $user ? sprintf('/admin/user/get-image?id=%d', $user->getId()) : null;
                $date = Carbon::createFromTimestamp($note->getDate());
                $statesHistory[] = [
                    'icon' => 'coreshop_icon_orderstates',
                    'type' => $note->getType(),
                    'date' => $date->formatLocalized('%A %d %B %Y %H:%M:%S'),
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

    protected function getPayments(OrderInterface $order)
    {
        $payments = $this->paymentRepository->findForPayable($order);
        $return = [];

        foreach ($payments as $payment) {
            $details = [];
            if (is_array($payment->getDetails()) && count($payment->getDetails()) > 0) {
                foreach ($payment->getDetails() as $detailName => $detailValue) {
                    if (empty($detailValue) && $detailValue != 0) {
                        continue;
                    }
                    if (is_array($detailValue)) {
                        $detailValue = join(', ', $detailValue);
                    }

                    $details[] = [$detailName, $detailValue ? htmlentities($detailValue) : ''];
                }
            }

            $availableTransitions = $this->workflowStateManager->parseTransitions($payment, 'coreshop_payment', [
                'cancel',
                'complete',
                'refund',
            ], false);

            $return[] = [
                'id' => $payment->getId(),
                'datePayment' => $payment->getDatePayment() ? $payment->getDatePayment()->getTimestamp() : '',
                'provider' => $payment->getPaymentProvider()->getIdentifier(),
                'paymentNumber' => $payment->getNumber(),
                'details' => $details,
                'amount' => $payment->getTotalAmount(),
                'stateInfo' => $this->workflowStateManager->getStateInfo('coreshop_payment', $payment->getState(), false),
                'transitions' => $availableTransitions,
            ];
        }

        return $return;
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
        if ($data instanceof DataObject\Concrete) {
            $dataLoader = new DataLoader();

            return $dataLoader->getDataForObject($data);
        }

        return [];
    }

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function setObjectNoteService(NoteServiceInterface $objectNoteService): void
    {
        $this->objectNoteService = $objectNoteService;
    }

    public function setAddressFormatter(AddressFormatterInterface $addressFormatter): void
    {
        $this->addressFormatter = $addressFormatter;
    }

    public function setSerializer(ArrayTransformerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }

    public function setWorkflowStateManager(WorkflowStateInfoManagerInterface $workflowStateManager): void
    {
        $this->workflowStateManager = $workflowStateManager;
    }

    public function setInvoiceProcessableHelper(ProcessableInterface $invoiceProcessableHelper): void
    {
        $this->invoiceProcessableHelper = $invoiceProcessableHelper;
    }

    public function setShipmentProcessableHelper(ProcessableInterface $shipmentProcessableHelper): void
    {
        $this->shipmentProcessableHelper = $shipmentProcessableHelper;
    }

    public function setOrderInvoiceRepository(OrderInvoiceRepositoryInterface $orderInvoiceRepository): void
    {
        $this->orderInvoiceRepository = $orderInvoiceRepository;
    }

    public function setOrderShipmentRepository(OrderShipmentRepositoryInterface $orderShipmentRepository): void
    {
        $this->orderShipmentRepository = $orderShipmentRepository;
    }

    public function setPaymentRepository(PaymentRepositoryInterface $paymentRepository): void
    {
        $this->paymentRepository = $paymentRepository;
    }
}
