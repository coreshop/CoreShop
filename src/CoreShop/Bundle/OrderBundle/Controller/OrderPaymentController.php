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
use CoreShop\Bundle\ResourceBundle\Controller\PimcoreController;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Payment\PaymentTransitions;
use CoreShop\Component\Payment\Repository\PaymentRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Resource\TokenGenerator\UniqueTokenGenerator;
use CoreShop\Component\Resource\Workflow\StateMachineManager;
use Symfony\Component\HttpFoundation\Request;


class OrderPaymentController extends PimcoreController
{
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

        $values = $request->request->all();
        unset($values['state']);

        $payment->setValues($values);

        $this->getEntityManager()->persist($payment);
        $this->getEntityManager()->flush();

        return $this->viewHandler->handle(['success' => true]);
    }

    /**
     * @param Request $request
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
                    'id'   => $provider->getId(),
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
                $orderNumber = preg_replace('/[^A-Za-z0-9\-_]/', '', str_replace(' ', '_', $order->getOrderNumber())) . '_' . $uniqueId;

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

                $workflow = $this->getStateMachineManager()->get($payment, 'coreshop_payment');
                $workflow->apply($payment, PaymentTransitions::TRANSITION_CREATE);

                return $this->viewHandler->handle([
                    'success'    => true,
                    'totalPayed' => $order->getTotalPayed()
                ]);
            }
        } else {
            return $this->viewHandler->handle(['success' => false, 'message' => "Payment Provider '$paymentProvider' not found"]);
        }
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

}
