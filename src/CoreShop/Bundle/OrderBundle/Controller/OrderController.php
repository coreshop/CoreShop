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
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Payment\PaymentTransitions;
use CoreShop\Component\Payment\Repository\PaymentRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Resource\TokenGenerator\UniqueTokenGenerator;
use CoreShop\Component\Resource\Workflow\StateMachineApplier;
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
                'text' => 'coreshop_workflow_name_coreshop_order',
                'type' => null,
                'dataIndex' => 'orderState',
                'renderAs' => 'orderState',
                'flex' => 1
            ],
            [
                'text' => 'coreshop_workflow_name_coreshop_order_payment',
                'type' => null,
                'dataIndex' => 'orderPaymentState',
                'renderAs' => 'orderPaymentState',
                'flex' => 1
            ],
            [
                'text' => 'coreshop_workflow_name_coreshop_order_shipment',
                'type' => null,
                'dataIndex' => 'orderShippingState',
                'renderAs' => 'orderShippingState',
                'flex' => 1
            ],
            [
                'text' => 'coreshop_workflow_name_coreshop_order_invoice',
                'type' => null,
                'dataIndex' => 'orderInvoiceState',
                'renderAs' => 'orderInvoiceState',
                'flex' => 1
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
     * @param Request $request
     * @return mixed
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updatePaymentAction(Request $request)
    {
        $payment = $this->getPaymentRepository()->find($request->get('id'));
        $order = $this->getSaleRepository()->find($payment->getOrderId());

        if (!$payment instanceof PaymentInterface) {
            return $this->viewHandler->handle(['success' => false]);
        }

        if (!$order instanceof OrderInterface) {
            return $this->viewHandler->handle(['success' => false]);
        }

        $payment->setValues($request->request->all());

        $this->getEntityManager()->persist($payment);
        $this->getEntityManager()->flush();

        $this->get('coreshop.state_machine_resolver.order_payment')->resolve($order);

        return $this->viewHandler->handle(['success' => true]);
    }

    /**
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function getPaymentProvidersAction()
    {
        $providers = $this->getPaymentProviderRepository()->findAll();
        $result = [];
        foreach ($providers as $provider) {
            if ($provider instanceof PaymentProviderInterface) {
                $result[] = [
                    'name' => $provider->getName(),
                    'id' => $provider->getId(),
                ];
            }
        }
        return $this->viewHandler->handle(['success' => true, 'data' => $result]);
    }

    /**
     * @param Request $request
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addPaymentAction(Request $request)
    {
        $orderId = $request->get('o_id');
        $order = $this->getSaleRepository()->find($orderId);
        $amount = doubleval($request->get('amount', 0));

        $paymentProviderId = $request->get('paymentProvider');

        if (!$order instanceof OrderInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => 'Order with ID "' . $orderId . '" not found']);
        }

        $paymentProvider = $this->getPaymentProviderRepository()->find($paymentProviderId);

        if ($paymentProvider instanceof PaymentProviderInterface) {
            $payedTotal = $order->getTotalPayed();

            $payedTotal += $amount;

            if ($payedTotal > $order->getTotal()) {
                return $this->viewHandler->handle(['success' => false, 'message' => 'Payed Amount is greater than order amount']);
            } else {
                /**
                 * @var PaymentInterface|PimcoreModelInterface
                 */
                $tokenGenerator = new UniqueTokenGenerator(true);
                $uniqueId = $tokenGenerator->generate(15);
                $orderNumber = $order->getOrderNumber() . '_' . $uniqueId;

                $payment = $this->getPaymentFactory()->createNew();
                $payment->setNumber($orderNumber);
                $payment->setPaymentProvider($paymentProvider);
                $payment->setCurrency($order->getCurrency());
                $payment->setTotalAmount($order->getTotal());
                $payment->setState(PaymentInterface::STATE_NEW);
                $payment->setDatePayment(Carbon::now());
                $payment->setOrderId($order->getId());

                $this->getEntityManager()->persist($payment);
                $this->getEntityManager()->flush();

                $this->get('coreshop.state_machine_resolver.order_payment')->resolve($order);

                return $this->viewHandler->handle(['success' => true, 'payments' => $this->getPayments($order), 'totalPayed' => $order->getTotalPayed()]);
            }
        } else {
            return $this->viewHandler->handle(['success' => false, 'message' => "Payment Provider '$paymentProvider' not found"]);
        }
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
            if(is_array($payment->getDetails()) && count($payment->getDetails()) > 0) {
                foreach($payment->getDetails() as $detailName => $detailValue) {
                    if(empty($detailValue) && $detailValue != 0) {
                        continue;
                    }
                    $details[] = [$detailName, $detailValue];
                }
            }

            $return[] = [
                'id' => $payment->getId(),
                'datePayment' => $payment->getDatePayment() ? $payment->getDatePayment()->getTimestamp() : '',
                'provider' => $payment->getPaymentProvider()->getName(),
                'details' => $details,
                //'transactionNotes' => $noteList->load(),
                'amount' => $payment->getTotalAmount(),
                'state' => $payment->getState(),
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
            $order['paymentFee'] = $sale->getPaymentFee();
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

    protected function getSummary(SaleInterface $sale)
    {
        $summary = parent::getSummary($sale);

        if ($sale instanceof OrderInterface) {
            if ($sale->getPaymentFee() > 0) {
                $summary[] = [
                    'key' => 'payment',
                    'value' => $sale->getPaymentFee(),
                ];
            }
        }

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
