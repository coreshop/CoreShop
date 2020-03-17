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
use CoreShop\Bundle\FrontendBundle\TemplateConfigurator\TemplateConfiguratorInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\OrderTransitions;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Payment\Repository\PaymentRepositoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\ClickableInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderController extends FrontendController
{
    public function reviseAction(
        Request $request,
        OrderRepositoryInterface $orderRepository,
        PaymentRepositoryInterface $paymentRepository,
        FormFactoryInterface $formFactory,
        TemplateConfiguratorInterface $templateConfigurator
    ): Response
    {
        $token = $request->get('token');
        $payment = null;

        /** @var OrderInterface $order */
        $order = $orderRepository->findOneBy(['token' => $token]);

        if (!$order instanceof OrderInterface) {
            throw new NotFoundHttpException();
        }

        if ($request->query->has('paymentId')) {
            $paymentObject = $paymentRepository->find($request->query->get('paymentId'));
            if ($paymentObject instanceof PaymentInterface) {
                $payment = $paymentObject;
            }
        }

        $form = $formFactory->createNamed('', PaymentType::class, $order, [
            'payment_subject' => $order,
        ]);

        if ($request->isMethod('post')) {
            $form = $form->handleRequest($request);

            $cancelButton = $form->get('cancel');

            if ($cancelButton instanceof ClickableInterface && $form->isSubmitted() && $cancelButton->isClicked()) {
                throw new \Exception('fix me');
//                $this->get('coreshop.state_machine_applier')->apply($cart, OrderTransitions::IDENTIFIER, OrderTransitions::TRANSITION_CANCEL);
//
//                if ($cart instanceof OrderInterface) {
//                    $cart->setState('cart');
//
//                    $this->get('coreshop.cart.manager')->persistCart($cart);
//
//                    $session = $request->getSession();
//                    $session->set(
//                        sprintf('%s.%s', $this->getParameter('coreshop.session.cart'), $cart->getStore()->getId()),
//                        $cart->getId()
//                    );
//
//                    return $this->redirectToRoute('coreshop_cart_summary');
//                }

                return $this->redirectToRoute('coreshop_index');
            } elseif ($form->isValid()) {
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

        return $this->renderTemplate($templateConfigurator->findTemplate('Order/revise.html'), $args);
    }
}
