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

use CoreShop\Bundle\OrderBundle\Form\Type\OrderInvoiceCreationType;
use CoreShop\Bundle\ResourceBundle\Controller\PimcoreController;
use CoreShop\Bundle\ResourceBundle\Controller\ViewHandlerInterface;
use CoreShop\Bundle\ResourceBundle\Form\Helper\ErrorSerializer;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManager;
use CoreShop\Component\Order\InvoiceStates;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\OrderInvoiceTransitions;
use CoreShop\Component\Order\Processable\ProcessableInterface;
use CoreShop\Component\Order\Renderer\OrderDocumentRendererInterface;
use CoreShop\Component\Order\Repository\OrderInvoiceRepositoryInterface;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\Order\Transformer\OrderDocumentTransformerInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderInvoiceController extends PimcoreController
{
    public function getInvoiceAbleItemsAction(
        Request $request,
        OrderRepositoryInterface $orderRepository,
        EventDispatcherInterface $eventDispatcher,
        ViewHandlerInterface $viewHandler
    ): Response {
        $orderId = $request->get('id');
        $order = $orderRepository->find($orderId);

        if (!$order instanceof OrderInterface) {
            return $viewHandler->handle(['success' => false, 'message' => 'Order with ID "'.$orderId.'" not found']);
        }

        $itemsToReturn = [];

        if (!$this->getProcessableHelper()->isProcessable($order)) {
            return $viewHandler->handle([
                'success' => false,
                'message' => 'The current order state does not allow to create invoices',
            ]);
        }

        try {
            $items = $this->getProcessableHelper()->getProcessableItems($order);
        } catch (\Exception $e) {
            return $viewHandler->handle(['success' => false, 'message' => $e->getMessage()]);
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

                $eventDispatcher->dispatch('coreshop.order.invoice.prepare_invoice_able', $event);

                $itemsToReturn[] = $event->getArguments();
            }
        }

        return $viewHandler->handle(['success' => true, 'items' => $itemsToReturn]);
    }

    public function createInvoiceAction(
        Request $request,
        FormFactoryInterface $formFactory,
        OrderRepositoryInterface $orderRepository,
        FactoryInterface $orderInvoiceFactory,
        StateMachineManager $stateMachineManager,
        ErrorSerializer $errorSerializer,
        ViewHandlerInterface $viewHandler
    ): Response {
        $orderId = $request->get('id');

        $form = $formFactory->createNamed('', OrderInvoiceCreationType::class);

        $handledForm = $form->handleRequest($request);

        if ($request->getMethod() === 'POST') {
            if (!$handledForm->isValid()) {
                return $viewHandler->handle(
                    [
                        'success' => false,
                        'message' => $errorSerializer->serializeErrorFromHandledForm($form),
                    ]
                );
            }

            $resource = $handledForm->getData();

            $order = $orderRepository->find($orderId);

            if (!$order instanceof OrderInterface) {
                return $viewHandler->handle([
                    'success' => false,
                    'message' => "Order with ID '$orderId' not found",
                ]);
            }

            try {
                // request invoice ready state from order, if it's our first invoice.
                $workflow = $stateMachineManager->get($order, 'coreshop_order_invoice');
                if ($workflow->can($order, OrderInvoiceTransitions::TRANSITION_REQUEST_INVOICE)) {
                    $workflow->apply($order, OrderInvoiceTransitions::TRANSITION_REQUEST_INVOICE);
                }

                $invoice = $orderInvoiceFactory->createNew();
                $invoice->setState(InvoiceStates::STATE_NEW);

                foreach ($resource as $key => $value) {
                    if (in_array($key, ['items', 'id', 'state'])) {
                        continue;
                    }

                    $invoice->setValue($key, $value);
                }

                $items = $resource['items'];
                $invoice = $this->getOrderToInvoiceTransformer()->transform($order, $invoice, $items);

                return $viewHandler->handle(['success' => true, 'invoiceId' => $invoice->getId()]);
            } catch (\Exception $ex) {
                return $viewHandler->handle(['success' => false, 'message' => $ex->getMessage()]);
            }
        }

        return $viewHandler->handle(['success' => false, 'message' => 'Method not supported, use POST']);
    }

    public function updateStateAction(
        Request $request,
        OrderInvoiceRepositoryInterface $orderInvoiceRepository,
        StateMachineManager $stateMachineManager,
        ViewHandlerInterface $viewHandler
    ): Response {
        $invoiceId = $request->get('id');
        $invoice = $orderInvoiceRepository->find($invoiceId);
        $transition = $request->get('transition');

        if (!$invoice instanceof OrderInvoiceInterface) {
            return $viewHandler->handle(['success' => false, 'message' => 'invalid shipment']);
        }

        //apply state machine
        $workflow = $stateMachineManager->get($invoice, InvoiceStates::IDENTIFIER);
        if (!$workflow->can($invoice, $transition)) {
            return $viewHandler->handle(['success' => false, 'message' => 'this transition is not allowed.']);
        }

        $workflow->apply($invoice, $transition);

        return $viewHandler->handle(['success' => true]);
    }

    public function renderAction(
        Request $request,
        OrderInvoiceRepositoryInterface $orderInvoiceRepository,
        OrderDocumentRendererInterface $orderDocumentRenderer
    ): Response {
        $invoiceId = $request->get('id');
        $invoice = $orderInvoiceRepository->find($invoiceId);

        if ($invoice instanceof OrderInvoiceInterface) {
            try {
                $responseData = $orderDocumentRenderer->renderDocumentPdf($invoice);
                $header = [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="invoice-'.$invoice->getId().'.pdf"',
                ];
            } catch (\Exception $e) {
                $responseData = '<strong>'.$e->getMessage().'</strong><br>trace: '.$e->getTraceAsString();
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
     * @return OrderDocumentTransformerInterface
     */
    private function getOrderToInvoiceTransformer()
    {
        return $this->get('coreshop.order.transformer.order_to_invoice');
    }
}
