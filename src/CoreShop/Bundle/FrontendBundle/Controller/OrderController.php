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

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Bundle\CoreBundle\Form\Type\Order\PaymentType;
use CoreShop\Bundle\WorkflowBundle\History\HistoryLogger;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\OrderTransitions;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Payment\Repository\PaymentRepositoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\Form\ClickableInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderController extends FrontendController
{
    /**
     * @param Request $request
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function reviseAction(Request $request)
    {
        $token = $request->get('token');
        $payment = null;

        /** @var OrderInterface $order */
        $order = $this->getOrderRepository()->findOneBy(['token' => $token]);

        if (!$order instanceof OrderInterface) {
            throw new NotFoundHttpException();
        }

        if ($request->query->has('paymentId')) {
            $paymentObject = $this->getPaymentRepository()->find($request->query->get('paymentId'));
            if ($paymentObject instanceof PaymentInterface) {
                $payment = $paymentObject;
            }
        }

        foreach ($this->getPaymentRepository()->findForPayable($order) as $payment) {
            if ($payment->getState() === PaymentInterface::STATE_COMPLETED) {
                $this->addFlash('error', $this->get('translator')->trans('coreshop.ui.error.order_already_paid'));

                return $this->redirectToRoute('coreshop_index');
            }
        }

        $form = $this->getFormFactory()->createNamed('', PaymentType::class, $order, [
            'payment_subject' => $order,
        ]);

        if ($request->isMethod('post')) {
            $form = $form->handleRequest($request);

            $cancelButton = $form->get('cancel');

            if ($cancelButton instanceof ClickableInterface && $form->isSubmitted() && $cancelButton->isClicked()) {
                throw new \Exception('fix me');
//
//                $this->get('coreshop.state_machine_applier')->apply($order, OrderTransitions::IDENTIFIER, OrderTransitions::TRANSITION_CANCEL);
//
//                if ($order instanceof Concrete) {
//                    $this->get(HistoryLogger::class)->log(
//                        $order,
//                        'User Cart Revise Cancellation'
//                    );
//                }
//
//                $cart = $this->get('coreshop.repository.cart')->findCartByOrder($order);
//
//                if ($cart instanceof CartInterface) {
//                    $cart->setOrder(null);
//
//                    $this->get('coreshop.cart.manager')->persistCart($cart);
//
//                    $session = $request->getSession();
//                    $session->set(
//                        sprintf('%s.%s', $this->container->getParameter('coreshop.session.cart'), $cart->getStore()->getId()),
//                        $cart->getId()
//                    );
//
//                    return $this->redirectToRoute('coreshop_cart_summary');
//                }

                return $this->redirectToRoute('coreshop_index');
            }

            if ($form->isValid()) {
                $order = $form->getData();
                $order->save();

                return $this->redirectToRoute('coreshop_order_revise_pay', ['token' => $token]);
            }
        }

        $args = [
            'order' => $order,
            'payment' => $payment,
            'form' => $form->createView(),
        ];

        return $this->render($this->templateConfigurator->findTemplate('Order/revise.html'), $args);
    }

    /**
     * @return OrderRepositoryInterface
     */
    protected function getOrderRepository()
    {
        return $this->get('coreshop.repository.order');
    }

    /**
     * @return PaymentRepositoryInterface
     */
    private function getPaymentRepository()
    {
        return $this->get('coreshop.repository.payment');
    }

    /**
     * @return FormFactoryInterface
     */
    protected function getFormFactory()
    {
        return $this->get('form.factory');
    }
}
