<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Bundle\OrderBundle\Form\Type\OrderInvoiceCreationType;
use CoreShop\Bundle\ResourceBundle\Controller\PimcoreController;
use CoreShop\Component\Order\InvoiceStates;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\OrderInvoiceTransitions;
use CoreShop\Component\Order\Processable\ProcessableInterface;
use CoreShop\Component\Order\Renderer\OrderDocumentRendererInterface;
use CoreShop\Component\Order\Transformer\OrderDocumentTransformerInterface;
use CoreShop\Component\Resource\Factory\PimcoreFactoryInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManager;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderInvoiceController extends PimcoreController
{
    /**
     * @param Request $request
     *
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function getInvoiceAbleItemsAction(Request $request)
    {
        $orderId = $request->get('id');
        $order = $this->getOrderRepository()->find($orderId);

        if (!$order instanceof OrderInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => 'Order with ID "' . $orderId . '" not found']);
        }

        $itemsToReturn = [];

        if (!$this->getProcessableHelper()->isProcessable($order)) {
            return $this->viewHandler->handle(['success' => false, 'message' => 'The current order state does not allow to create invoices']);
        }

        try {
            $items = $this->getProcessableHelper()->getProcessableItems($order);
        } catch (\Exception $e) {
            return $this->viewHandler->handle(['success' => false, 'message' => $e->getMessage()]);
        }

        foreach ($items as $item) {
            $orderItem = $item['item'];
            if ($orderItem instanceof OrderItemInterface) {
                $itemToReturn = [
                    'orderItemId' => $orderItem->getId(),
                    'price' => $orderItem->getItemPrice(),
                    'maxToInvoice' => $item['quantity'],
                    'quantity' => $orderItem->getQuantity(),
                    'quantityInvoiced' => $orderItem->getQuantity() - $item['quantity'],
                    'toInvoice' => $item['quantity'],
                    'tax' => $orderItem->getTotalTax(),
                    'total' => $orderItem->getTotal(),
                    'name' => $orderItem->getName(),
                ];

                $event = new GenericEvent($orderItem, $itemToReturn);

                $this->get('event_dispatcher')->dispatch('coreshop.order.invoice.prepare_invoice_able', $event);

                $itemsToReturn[] = $event->getArguments();
            }
        }

        return $this->viewHandler->handle(['success' => true, 'items' => $itemsToReturn]);
    }

    /**
     * @param Request $request
     *
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function createInvoiceAction(Request $request)
    {
        $orderId = $request->get('id');

        $form = $this->get('form.factory')->createNamed('', OrderInvoiceCreationType::class);

        $handledForm = $form->handleRequest($request);

        if ($request->getMethod() === 'POST') {
            if (!$handledForm->isValid()) {
                return $this->viewHandler->handle(
                    [
                        'success' => false,
                        'message' => $this->get('coreshop.resource.helper.form_error_serializer')->serializeErrorFromHandledForm($form),
                    ]
                );
            }

            $resource = $handledForm->getData();

            $order = $this->getOrderRepository()->find($orderId);

            if (!$order instanceof OrderInterface) {
                return $this->viewHandler->handle([
                    'success' => false,
                    'message' => "Order with ID '$orderId' not found",
                ]);
            }

            try {
                // request invoice ready state from order, if it's our first invoice.
                $workflow = $this->getStateMachineManager()->get($order, 'coreshop_order_invoice');
                if ($workflow->can($order, OrderInvoiceTransitions::TRANSITION_REQUEST_INVOICE)) {
                    $workflow->apply($order, OrderInvoiceTransitions::TRANSITION_REQUEST_INVOICE);
                }

                $invoice = $this->getInvoiceFactory()->createNew();
                $invoice->setState(InvoiceStates::STATE_NEW);

                foreach ($resource as $key => $value) {
                    if (in_array($key, ['items', 'id', 'state'])) {
                        continue;
                    }

                    $invoice->setValue($key, $value);
                }

                $items = $resource['items'];
                $invoice = $this->getOrderToInvoiceTransformer()->transform($order, $invoice, $items);

                return $this->viewHandler->handle(['success' => true, 'invoiceId' => $invoice->getId()]);
            } catch (\Exception $ex) {
                return $this->viewHandler->handle(['success' => false, 'message' => $ex->getMessage()]);
            }
        }

        return $this->viewHandler->handle(['success' => false, 'message' => 'Method not supported, use POST']);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function updateStateAction(Request $request)
    {
        $invoiceId = $request->get('id');
        $invoice = $this->getOrderInvoiceRepository()->find($invoiceId);
        $transition = $request->get('transition');

        if (!$invoice instanceof OrderInvoiceInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => 'invalid shipment']);
        }

        //apply state machine
        $workflow = $this->getStateMachineManager()->get($invoice, InvoiceStates::IDENTIFIER);
        if (!$workflow->can($invoice, $transition)) {
            return $this->viewHandler->handle(['success' => false, 'message' => 'this transition is not allowed.']);
        }

        $workflow->apply($invoice, $transition);

        return $this->viewHandler->handle(['success' => true]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function renderAction(Request $request)
    {
        $invoiceId = $request->get('id');
        $invoice = $this->getOrderInvoiceRepository()->find($invoiceId);

        if ($invoice instanceof OrderInvoiceInterface) {
            try {
                $responseData = $this->getOrderDocumentRenderer()->renderDocumentPdf($invoice);
                $header = [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="invoice-' . $invoice->getId() . '.pdf"',
                ];
            } catch (\Exception $e) {
                $responseData = '<strong>' . $e->getMessage() . '</strong><br>trace: ' . $e->getTraceAsString();
                $header = ['Content-Type' => 'text/html'];
            }

            return new Response($responseData, 200, $header);
        }

        throw new NotFoundHttpException(sprintf('Invoice with Id %s not found', $invoiceId));
    }

    /**
     * @return ProcessableInterface
     */
    private function getProcessableHelper()
    {
        return $this->get('coreshop.order.invoice.processable');
    }

    /**
     * @return PimcoreRepositoryInterface
     */
    private function getOrderRepository()
    {
        return $this->get('coreshop.repository.order');
    }

    /**
     * @return OrderDocumentRendererInterface
     */
    private function getOrderDocumentRenderer()
    {
        return $this->get('coreshop.renderer.order.pdf');
    }

    /**
     * @return PimcoreRepositoryInterface
     */
    private function getOrderInvoiceRepository()
    {
        return $this->get('coreshop.repository.order_invoice');
    }

    /**
     * @return PimcoreFactoryInterface
     */
    private function getInvoiceFactory()
    {
        return $this->get('coreshop.factory.order_invoice');
    }

    /**
     * @return OrderDocumentTransformerInterface
     */
    private function getOrderToInvoiceTransformer()
    {
        return $this->get('coreshop.order.transformer.order_to_invoice');
    }

    /**
     * @return StateMachineManager
     */
    protected function getStateMachineManager()
    {
        return $this->get('coreshop.state_machine_manager');
    }
}
