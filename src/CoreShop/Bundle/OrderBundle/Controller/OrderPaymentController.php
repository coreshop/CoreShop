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

declare(strict_types=1);

namespace CoreShop\Bundle\OrderBundle\Controller;

use Carbon\Carbon;
use CoreShop\Bundle\ResourceBundle\Controller\PimcoreController;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManager;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderPaymentInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Payment\PaymentTransitions;
use CoreShop\Component\Payment\Repository\PaymentProviderRepositoryInterface;
use CoreShop\Component\Payment\Repository\PaymentRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\TokenGenerator\UniqueTokenGenerator;
use Symfony\Component\HttpFoundation\Request;

class OrderPaymentController extends PimcoreController
{
    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function updateStateAction(Request $request)
    {
        $payment = $this->getPaymentRepository()->find($request->get('id'));
        $transition = $request->get('transition');

        if (!$payment instanceof PaymentInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => 'invalid payment']);
        }

        //apply state machine
        $workflow = $this->getStateMachineManager()->get($payment, 'coreshop_payment');
        if (!$workflow->can($payment, $transition)) {
            return $this->viewHandler->handle(['success' => false, 'message' => 'this transition is not allowed.']);
        }

        $workflow->apply($payment, $transition);

        return $this->viewHandler->handle(['success' => true]);
    }

    /**
     * @param Request $request
     *
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addPaymentAction(Request $request)
    {
        //TODO: Use Form here

        $orderId = $request->get('o_id');
        $order = $this->getSaleRepository()->find($orderId);
        $amount = (float) $request->get('amount', 0) * $this->container->getParameter('coreshop.currency.decimal_factor');

        $paymentProviderId = $request->get('paymentProvider');

        if (!$order instanceof OrderInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => 'Order with ID "' . $orderId . '" not found']);
        }

        $payments = $this->getPaymentRepository()->findForPayable($order);
        $paymentProvider = $this->getPaymentProviderRepository()->find($paymentProviderId);
        $totalPayed = array_sum(array_map(function (PaymentInterface $payment) {
            if ($payment->getState() === PaymentInterface::STATE_CANCELLED ||
                $payment->getState() === PaymentInterface::STATE_FAILED ||
                $payment->getState() === PaymentInterface::STATE_REFUNDED) {
                return 0;
            }

            return $payment->getTotalAmount();
        }, $payments));

        if ($paymentProvider instanceof PaymentProviderInterface) {
            $totalPaymentWouldBe = $totalPayed + $amount;

            if ($totalPaymentWouldBe > $order->getTotal()) {
                return $this->viewHandler->handle([
                    'success' => false,
                    'message' => 'Payed Amount is greater than order amount'
                ]);
            }

            $tokenGenerator = new UniqueTokenGenerator(true);
            $uniqueId = $tokenGenerator->generate(15);
            $orderNumber = preg_replace('/[^A-Za-z0-9\-_]/', '', str_replace(' ', '_', $order->getOrderNumber())) . '_' . $uniqueId;

            /**
             * @var PaymentInterface $payment
             */
            $payment = $this->getPaymentFactory()->createNew();
            $payment->setNumber($orderNumber);
            $payment->setPaymentProvider($paymentProvider);

            if (method_exists($payment, 'setCurrency')) {
                $payment->setCurrency($order->getBaseCurrency());
            }

            $payment->setTotalAmount($amount);
            $payment->setState(PaymentInterface::STATE_NEW);
            $payment->setDatePayment(Carbon::now());

            if ($payment instanceof OrderPaymentInterface) {
                $payment->setOrder($order);
            }

            $this->getEntityManager()->persist($payment);
            $this->getEntityManager()->flush();

            $workflow = $this->getStateMachineManager()->get($payment, 'coreshop_payment');
            $workflow->apply($payment, PaymentTransitions::TRANSITION_PROCESS);

            return $this->viewHandler->handle([
                'success' => true,
                'totalPayed' => $totalPayed,
            ]);

            $tokenGenerator = new UniqueTokenGenerator(true);
            $uniqueId = $tokenGenerator->generate(15);
            $orderNumber = preg_replace('/[^A-Za-z0-9\-_]/', '', str_replace(' ', '_', $order->getOrderNumber())) . '_' . $uniqueId;

            /**
             * @var PaymentInterface $payment
             */
            $payment = $this->getPaymentFactory()->createNew();
            $payment->setNumber($orderNumber);
            $payment->setPaymentProvider($paymentProvider);
            $payment->setCurrency($order->getCurrency());
            $payment->setTotalAmount($amount);
            $payment->setState(PaymentInterface::STATE_NEW);
            $payment->setDatePayment(Carbon::now());

            if ($payment instanceof OrderPaymentInterface) {
                $payment->setOrder($order);
            }

            $this->getEntityManager()->persist($payment);
            $this->getEntityManager()->flush();

            $workflow = $this->getStateMachineManager()->get($payment, 'coreshop_payment');
            $workflow->apply($payment, PaymentTransitions::TRANSITION_PROCESS);

            return $this->viewHandler->handle([
                'success' => true,
                'totalPayed' => $totalPayed,
            ]);
        }

        return $this->viewHandler->handle(
            [
                'success' => false,
                'message' => sprintf('Payment Provider %s not found', $request->get('paymentProvider')),
            ]
        );
    }

    /**
     * @return PaymentRepositoryInterface
     */
    private function getPaymentRepository()
    {
        return $this->get('coreshop.repository.payment');
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEntityManager()
    {
        return $this->get('doctrine.orm.entity_manager');
    }

    /**
     * @return PaymentProviderRepositoryInterface
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
     * @return StateMachineManager
     */
    protected function getStateMachineManager()
    {
        return $this->get('coreshop.state_machine_manager');
    }

    protected function getSaleRepository()
    {
        return $this->get('coreshop.repository.order');
    }
}
