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

namespace CoreShop\Bundle\OrderBundle\Controller;

use Carbon\Carbon;
use CoreShop\Component\Order\OrderPaymentStates;
use CoreShop\Component\Order\OrderPaymentTransitions;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\SaleInterface;
use CoreShop\Component\Order\OrderInvoiceTransitions;
use CoreShop\Component\Order\OrderShipmentTransitions;
use CoreShop\Component\Order\OrderTransitions;
use CoreShop\Component\Order\Processable\ProcessableInterface;
use CoreShop\Component\Order\Repository\OrderInvoiceRepositoryInterface;
use CoreShop\Component\Order\Repository\OrderShipmentRepositoryInterface;
use CoreShop\Component\Order\Workflow\WorkflowStateManagerInterface;
use CoreShop\Component\Payment\PaymentTransitions;
use CoreShop\Component\Resource\Workflow\StateMachineApplier;
use CoreShop\Component\Resource\Workflow\StateMachineManager;
use Pimcore\Model\User;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends AbstractSaleDetailController
{
    /**
     * {@inheritdoc}
     */
    protected function getGridColumns()
    {
        return [
            [
                'text'      => 'coreshop_workflow_name_coreshop_order',
                'type'      => null,
                'dataIndex' => 'orderState',
                'renderAs'  => 'orderState',
                'flex'      => 1
            ],
            [
                'text'      => 'coreshop_workflow_name_coreshop_order_payment',
                'type'      => null,
                'dataIndex' => 'orderPaymentState',
                'renderAs'  => 'orderPaymentState',
                'flex'      => 1
            ],
            [
                'text'      => 'coreshop_workflow_name_coreshop_order_shipment',
                'type'      => null,
                'dataIndex' => 'orderShippingState',
                'renderAs'  => 'orderShippingState',
                'flex'      => 1
            ],
            [
                'text'      => 'coreshop_workflow_name_coreshop_order_invoice',
                'type'      => null,
                'dataIndex' => 'orderInvoiceState',
                'renderAs'  => 'orderInvoiceState',
                'flex'      => 1
            ]
        ];
    }

    /**
     * @param Request $request
     * @return bool
     * @throws \Exception
     */
    public function getStatesAction(Request $request)
    {
        $identifiers = [
            OrderTransitions::IDENTIFIER,
            OrderShipmentTransitions::IDENTIFIER,
            OrderPaymentTransitions::IDENTIFIER,
            OrderInvoiceTransitions::IDENTIFIER,
            PaymentTransitions::IDENTIFIER
        ];
        $states = [];
        $workflowStateManager = $this->getWorkflowStateManager();

        foreach ($identifiers as $identifier) {
            $places = $this->get(sprintf('state_machine.%s', $identifier))->getDefinition()->getPlaces();

            foreach ($places as $place) {
                $states[$identifier][] = $workflowStateManager->getStateInfo($identifier, $place, false);
            }
        }

        return $this->viewHandler->handle(['success' => true, 'states' => $states]);
    }

    /**
     * @param Request $request
     * @return bool
     * @throws \Exception
     */
    public function cancelOrderAction(Request $request)
    {
        $orderId = $request->get('o_id');
        $order = $this->getSaleRepository()->find($orderId);

        if (!$order instanceof OrderInterface) {
            throw new \Exception('invalid order');
        }

        $this->getStateMachineApplier()->apply($order, 'coreshop_order', 'cancel');

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

        $date = Carbon::now();
        $statesHistory = [];

        if (is_array($history)) {
            foreach ($history as $note) {
                $user = User::getById($note->getUser());
                $avatar = $user ? sprintf('/admin/user/get-image?id=%d', $user->getId()) : null;

                $statesHistory[] = [
                    'icon'        => 'coreshop_icon_orderstates',
                    'type'        => $note->getType(),
                    'date'        => $date->formatLocalized('%A %d %B %Y %H:%M:%S'),
                    'avatar'      => $avatar,
                    'user'        => $user ? $user->getName() : null,
                    'description' => $note->getDescription(),
                    'title'       => $note->getTitle(),
                    'data'        => $note->getData(),
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

            $details = [];
            if (is_array($payment->getDetails()) && count($payment->getDetails()) > 0) {
                foreach ($payment->getDetails() as $detailName => $detailValue) {
                    if (empty($detailValue) && $detailValue != 0) {
                        continue;
                    }
                    $details[] = [$detailName, $detailValue];
                }
            }

            $availableTransitions = $this->getWorkflowStateManager()->fulfillTransitions($payment, 'coreshop_payment', [
                'cancel',
                'complete',
                'refund'
            ], false);

            $return[] = [
                'id'            => $payment->getId(),
                'datePayment'   => $payment->getDatePayment() ? $payment->getDatePayment()->getTimestamp() : '',
                'provider'      => $payment->getPaymentProvider()->getName(),
                'paymentNumber' => $payment->getNumber(),
                'details'       => $details,
                //'transactionNotes' => $noteList->load(),
                'amount'        => $payment->getTotalAmount(),
                'stateInfo'     => $this->getWorkflowStateManager()->getStateInfo('coreshop_payment', $payment->getState(), false),
                'transitions'   => $availableTransitions
            ];
        }

        return $return;
    }

    /**
     * @param SaleInterface $sale
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

            $order['statesHistory'] = $this->getStatesHistory($sale);

            $order['payments'] = $this->getPayments($sale);
            $order['editable'] = count($this->getInvoices($sale)) > 0 ? false : true;
            $order['invoices'] = $this->getInvoices($sale);
            $order['shipments'] = $this->getShipments($sale);
            $order['invoiceCreationAllowed'] = !$this->getInvoiceProcessableHelper()->isFullyProcessed($sale) && $sale->getPaymentState() === OrderPaymentStates::STATE_PAID;
            $order['shipmentCreationAllowed'] = !$this->getShipmentProcessableHelper()->isFullyProcessed($sale) && $sale->getPaymentState() === OrderPaymentStates::STATE_PAID;
        }

        return $order;
    }

    /**
     * @param SaleInterface $sale
     * @return array
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

            $availableTransitions = $this->getWorkflowStateManager()->fulfillTransitions($invoice, 'coreshop_invoice', [
                'complete',
                'cancel'
            ], false);

            $data = $this->getDataForObject($invoice);

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
            $data = $this->getDataForObject($shipment);
            $data['carrierName'] = $shipment->getCarrier()->getName();

            $availableTransitions = $this->getWorkflowStateManager()->fulfillTransitions($shipment, 'coreshop_shipment', [
                'hold',
                'release',
                'prepare',
                'ship',
                'cancel',
                'return'
            ], false);

            $data['stateInfo'] = $this->getWorkflowStateManager()->getStateInfo('coreshop_shipment', $shipment->getState(), false);
            $data['transitions'] = $availableTransitions;

            // better solution?
            foreach ($shipment->getItems() as $index => $item) {
                $data['items'][$index]['_itemName'] = $item->getOrderItem()->getName();
            }

            $shipmentArray[] = $data;
        }

        return $shipmentArray;
    }

    protected function getSummary(SaleInterface $sale)
    {
        $summary = parent::getSummary($sale);
        return $summary;
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
     * @return WorkflowStateManagerInterface
     */
    private function getWorkflowStateManager()
    {
        return $this->get('coreshop.workflow.state_manager');
    }

    /**
     * @return StateMachineApplier
     */
    private function getStateMachineApplier()
    {
        return $this->get('coreshop.state_machine_applier');
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
        return 'coreshop.model.order.pimcore_class_id';
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
