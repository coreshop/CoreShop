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

use Carbon\Carbon;
use CoreShop\Bundle\WorkflowBundle\History\HistoryLogger;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManager;
use CoreShop\Bundle\WorkflowBundle\StateManager\WorkflowStateInfoManagerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\SaleInterface;
use CoreShop\Component\Order\OrderStates;
use CoreShop\Component\Order\OrderTransitions;
use CoreShop\Component\Order\Processable\ProcessableInterface;
use CoreShop\Component\Order\Repository\OrderInvoiceRepositoryInterface;
use CoreShop\Component\Order\Repository\OrderShipmentRepositoryInterface;
use CoreShop\Component\Pimcore\DataObject\NoteServiceInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\User;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Workflow\StateMachine;

class OrderController extends AbstractSaleDetailController
{
    /**
     * @return mixed
     *
     * @throws \Exception
     */
    public function getFolderConfigurationAction()
    {
        $this->isGrantedOr403();

        $name = null;
        $folderId = null;

        $orderClassId = $this->getParameter('coreshop.model.order.pimcore_class_name');
        $folderPath = $this->getParameter('coreshop.folder.order');
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

    /**
     * @param Request $request
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function getStatesAction(Request $request)
    {
        $identifiers = $this->getParameter('coreshop.state_machines');
        $states = [];
        $transitions = [];
        $workflowStateManager = $this->getWorkflowStateManager();

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
                $states[$identifier][] = $workflowStateManager->getStateInfo($identifier, $place, false);
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

    /**
     * @param Request $request
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function updateOrderStateAction(Request $request)
    {
        $orderId = $request->get('o_id');
        $order = $this->getSaleRepository()->find($orderId);
        $transition = $request->get('transition');

        if (!$order instanceof OrderInterface) {
            throw new \Exception('invalid order');
        }

        //apply state machine
        $workflow = $this->getStateMachineManager()->get($order, 'coreshop_order');
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

    /**
     * @param OrderInterface $order
     *
     * @return array
     */
    protected function getStatesHistory(OrderInterface $order)
    {
        //Get History
        $manager = $this->getWorkflowStateManager();
        $history = $manager->getStateHistory($order);

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

    /**
     * @param OrderInterface $order
     *
     * @return array
     */
    protected function getPayments(OrderInterface $order)
    {
        $payments = $this->get('coreshop.repository.payment')->findForPayable($order);
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

                    if (true === is_bool($detailValue)) {
                        if (true === $detailValue) {
                            $detailValue = 'true';
                        } else {
                            $detailValue = 'false';
                        }
                    }

                    if (false === is_string($detailValue)) {
                        $detailValue = (string)$detailValue;
                    }

                    $details[] = [$detailName, htmlentities($detailValue)];
                }
            }

            $availableTransitions = $this->getWorkflowStateManager()->parseTransitions($payment, 'coreshop_payment', [
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
                'stateInfo' => $this->getWorkflowStateManager()->getStateInfo('coreshop_payment', $payment->getState(), false),
                'transitions' => $availableTransitions,
            ];
        }

        return $return;
    }

    /**
     * @param SaleInterface $sale
     *
     * @return array
     */
    protected function getDetails(SaleInterface $sale)
    {
        $order = parent::getDetails($sale);

        if ($sale instanceof OrderInterface) {
            $workflowStateManager = $this->getWorkflowStateManager();
            $order['orderState'] = $workflowStateManager->getStateInfo('coreshop_order', $sale->getOrderState(), false);
            $order['orderPaymentState'] = $workflowStateManager->getStateInfo('coreshop_order_payment', $sale->getPaymentState(), false);
            $order['orderShippingState'] = $workflowStateManager->getStateInfo('coreshop_order_shipment', $sale->getShippingState(), false);
            $order['orderInvoiceState'] = $workflowStateManager->getStateInfo('coreshop_order_invoice', $sale->getInvoiceState(), false);

            $availableTransitions = $this->getWorkflowStateManager()->parseTransitions($sale, 'coreshop_order', [
                'cancel',
            ], false);

            $order['availableOrderTransitions'] = $availableTransitions;
            $order['statesHistory'] = $this->getStatesHistory($sale);


            $invoices = $this->getInvoices($sale);

            $order['editable'] = count($invoices) > 0 ? false : true;
            $order['invoices'] = $invoices;
            $order['payments'] = $this->getPayments($sale);
            $order['shipments'] = $this->getShipments($sale);
            $order['paymentCreationAllowed'] = !in_array($sale->getOrderState(), [OrderStates::STATE_CANCELLED, OrderStates::STATE_COMPLETE]);
            $order['invoiceCreationAllowed'] = $this->getInvoiceProcessableHelper()->isProcessable($sale);
            $order['shipmentCreationAllowed'] = $this->getShipmentProcessableHelper()->isProcessable($sale);
        }

        $event = new GenericEvent($sale, $order);

        $this->get('event_dispatcher')->dispatch('coreshop.order.prepare_details', $event);

        return $event->getArguments();
    }

    /**
     * @param SaleInterface $sale
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function prepareSale(SaleInterface $sale)
    {
        $order = parent::prepareSale($sale);
        $workflowStateManager = $this->getWorkflowStateManager();

        if ($sale instanceof OrderInterface) {
            $order['orderState'] = $workflowStateManager->getStateInfo('coreshop_order', $sale->getOrderState(), false);
            $order['orderPaymentState'] = $workflowStateManager->getStateInfo('coreshop_order_payment', $sale->getPaymentState(), false);
            $order['orderShippingState'] = $workflowStateManager->getStateInfo('coreshop_order_shipment', $sale->getShippingState(), false);
            $order['orderInvoiceState'] = $workflowStateManager->getStateInfo('coreshop_order_invoice', $sale->getInvoiceState(), false);
        }

        return $order;
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
            $availableTransitions = $this->getWorkflowStateManager()->parseTransitions($invoice, 'coreshop_invoice', [
                'complete',
                'cancel',
            ], false);

            if ($this->useLegacySerialization()) {
                $data = $this->getDataForObject($invoice);

                foreach ($invoice->getItems() as $index => $item) {
                    $data['items'][$index]['_itemName'] = $item->getOrderItem()->getName();
                }
            } else {
                $data = $this->getSerializer()->toArray($invoice);
            }

            $data['stateInfo'] = $this->getWorkflowStateManager()->getStateInfo('coreshop_invoice', $invoice->getState(), false);
            $data['transitions'] = $availableTransitions;

            $invoiceArray[] = $data;
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
        $shipments = $this->getOrderShipmentRepository()->getDocuments($order);
        $shipmentArray = [];

        foreach ($shipments as $shipment) {

            $availableTransitions = $this->getWorkflowStateManager()->parseTransitions($shipment, 'coreshop_shipment', [
                'create',
                'ship',
                'cancel',
            ], false);

            if ($this->useLegacySerialization()) {
                $data = $this->getDataForObject($shipment);

                foreach ($shipment->getItems() as $index => $item) {
                    $data['items'][$index]['_itemName'] = $item->getOrderItem()->getName();
                }

                $data['carrierName'] = $shipment->getCarrier() !== null ? $shipment->getCarrier()->getIdentifier() : null;
            } else {
                $data = $this->getSerializer()->toArray($shipment);
            }

            $data['stateInfo'] = $this->getWorkflowStateManager()->getStateInfo('coreshop_shipment', $shipment->getState(), false);
            $data['transitions'] = $availableTransitions;

            $shipmentArray[] = $data;
        }

        return $shipmentArray;
    }

    /**
     * @param SaleInterface $sale
     *
     * @return array
     */
    protected function getSummary(SaleInterface $sale)
    {
        $summary = parent::getSummary($sale);

        return $summary;
    }

    /**
     * @return ProcessableInterface
     */
    protected function getInvoiceProcessableHelper()
    {
        return $this->get('coreshop.order.invoice.processable');
    }

    /**
     * @return ProcessableInterface
     */
    protected function getShipmentProcessableHelper()
    {
        return $this->get('coreshop.order.shipment.processable');
    }

    /**
     * @return OrderInvoiceRepositoryInterface
     */
    protected function getOrderInvoiceRepository()
    {
        return $this->get('coreshop.repository.order_invoice');
    }

    /**
     * @return OrderShipmentRepositoryInterface
     */
    protected function getOrderShipmentRepository()
    {
        return $this->get('coreshop.repository.order_shipment');
    }

    /**
     * @return WorkflowStateInfoManagerInterface
     */
    protected function getWorkflowStateManager()
    {
        return $this->get('coreshop.workflow.state_info_manager');
    }

    /**
     * @return StateMachineManager
     */
    protected function getStateMachineManager()
    {
        return $this->get('coreshop.state_machine_manager');
    }

    /**
     * {@inheritdoc}
     */
    protected function getSaleRepository()
    {
        return $this->get('coreshop.repository.order');
    }

    /**
     * {@inheritdoc}
     */
    protected function getSalesList()
    {
        return $this->getSaleRepository()->getList();
    }

    /**
     * {@inheritdoc}
     */
    protected function getSaleClassName()
    {
        return 'coreshop.model.order.pimcore_class_name';
    }

    /**
     * {@inheritdoc}
     */
    protected function getOrderKey()
    {
        return 'orderDate';
    }

    /**
     * {@inheritdoc}
     */
    protected function getSaleNumberField()
    {
        return 'orderNumber';
    }

}
