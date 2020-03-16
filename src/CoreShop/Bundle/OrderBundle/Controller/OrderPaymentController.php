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
use CoreShop\Bundle\ResourceBundle\Controller\PimcoreController;
use CoreShop\Bundle\ResourceBundle\Controller\ViewHandlerInterface;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManager;
use CoreShop\Component\Core\Repository\PaymentProviderRepositoryInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderPaymentInterface;
use CoreShop\Component\Order\Repository\OrderItemRepositoryInterface;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Payment\PaymentTransitions;
use CoreShop\Component\Payment\Repository\PaymentRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\TokenGenerator\UniqueTokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderPaymentController extends PimcoreController
{
    public function updateStateAction(
        Request $request,
        PaymentRepositoryInterface $paymentRepository,
        StateMachineManager $stateMachineManager,
        ViewHandlerInterface $viewHandler
    ): Response
    {
        $payment = $paymentRepository->find($request->get('id'));
        $transition = $request->get('transition');

        if (!$payment instanceof PaymentInterface) {
            return $viewHandler->handle(['success' => false, 'message' => 'invalid payment']);
        }

        //apply state machine
        $workflow = $stateMachineManager->get($payment, 'coreshop_payment');
        if (!$workflow->can($payment, $transition)) {
            return $viewHandler->handle(['success' => false, 'message' => 'this transition is not allowed.']);
        }

        $workflow->apply($payment, $transition);

        return $viewHandler->handle(['success' => true]);
    }

    public function addPaymentAction(
        Request $request,
        OrderRepositoryInterface $orderRepository,
        PaymentRepositoryInterface $paymentRepository,
        PaymentProviderRepositoryInterface $paymentProviderRepository,
        FactoryInterface $paymentFactory,
        EntityManagerInterface $entityManager,
        StateMachineManager $stateMachineManager,
        ViewHandlerInterface $viewHandler
    ): Response
    {
        //TODO: Use Form here

        $orderId = $request->get('o_id');
        $order = $orderRepository->find($orderId);
        $amount = (float) $request->get('amount', 0) * $this->getParameter('coreshop.currency.decimal_factor');

        $paymentProviderId = $request->get('paymentProvider');

        if (!$order instanceof OrderInterface) {
            return $viewHandler->handle(['success' => false, 'message' => 'Order with ID "' . $orderId . '" not found']);
        }

        $payments = $paymentRepository->findForPayable($order);
        $paymentProvider = $paymentProviderRepository->find($paymentProviderId);
        $totalPayed = array_sum(array_map(function (PaymentInterface $payment) {
            if ($payment->getState() === PaymentInterface::STATE_CANCELLED ||
                $payment->getState() === PaymentInterface::STATE_REFUNDED) {
                return 0;
            }

            return $payment->getTotalAmount();
        }, $payments));

        if ($paymentProvider instanceof PaymentProviderInterface) {
            $totalPaymentWouldBe = $totalPayed + $amount;

            if ($totalPaymentWouldBe > $order->getTotal()) {
                return $viewHandler->handle(['success' => false, 'message' => 'Payed Amount is greater than order amount']);
            }

            $tokenGenerator = new UniqueTokenGenerator(true);
            $uniqueId = $tokenGenerator->generate(15);
            $orderNumber = preg_replace('/[^A-Za-z0-9\-_]/', '', str_replace(' ', '_', $order->getOrderNumber())) . '_' . $uniqueId;

            /**
             * @var PaymentInterface $payment
             */
            $payment = $paymentFactory->createNew();
            $payment->setNumber($orderNumber);
            $payment->setPaymentProvider($paymentProvider);

            if (method_exists($payment, 'setCurrency')) {
                $payment->setCurrency($order->getCurrency());
            }

            $payment->setTotalAmount($amount);
            $payment->setState(PaymentInterface::STATE_NEW);
            $payment->setDatePayment(Carbon::now());

            if ($payment instanceof OrderPaymentInterface) {
                $payment->setOrder($order);
            }

            $entityManager->persist($payment);
            $entityManager->flush();

            $workflow = $stateMachineManager->get($payment, 'coreshop_payment');
            $workflow->apply($payment, PaymentTransitions::TRANSITION_PROCESS);

            return $viewHandler->handle([
                'success' => true,
                'totalPayed' => $totalPayed,
            ]);
        }

        return $viewHandler->handle(
            [
                'success' => false,
                'message' => sprintf('Payment Provider %s not found', $request->get('paymentProvider')),
            ]
        );
    }
}
