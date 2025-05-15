<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Bundle\CoreBundle\Form\Type\Order\PaymentType;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Payment\Repository\PaymentRepositoryInterface;
use Symfony\Component\Form\ClickableInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends FrontendController
{
    public function reviseAction(Request $request): Response
    {
        $token = $this->getParameterFromRequest($request, 'token');
        $payment = null;

        /** @var OrderInterface $order */
        $order = $this->getOrderRepository()->findOneBy(['token' => $token]);

        if ($request->query->has('paymentId')) {
            $paymentObject = $this->getPaymentRepository()->find($request->query->get('paymentId'));
            if ($paymentObject instanceof PaymentInterface) {
                $payment = $paymentObject;
            }
        }

        foreach ($this->getPaymentRepository()->findForPayable($order) as $payment) {
            if ($payment->getState() === PaymentInterface::STATE_COMPLETED) {
                $this->addFlash('error', $this->container->get('translator')->trans('coreshop.ui.error.order_already_paid'));

                return $this->redirectToRoute('coreshop_index');
            }
        }

        $form = $this->getFormFactory()->createNamed('coreshop', PaymentType::class, $order, [
            'payment_subject' => $order,
        ]);

        if ($request->isMethod('post')) {
            $form = $form->handleRequest($request);

            $cancelButton = $form->get('cancel');

            if ($cancelButton instanceof ClickableInterface && $form->isSubmitted() && $cancelButton->isClicked()) {
                throw new \Exception('fix me');
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

        return $this->render($this->getTemplateConfigurator()->findTemplate('Order/revise.html'), $args);
    }

    protected function getOrderRepository(): OrderRepositoryInterface
    {
        return $this->container->get('coreshop.repository.order');
    }

    private function getPaymentRepository(): PaymentRepositoryInterface
    {
        return $this->container->get('coreshop.repository.payment');
    }

    protected function getFormFactory(): FormFactoryInterface
    {
        return $this->container->get('form.factory');
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                'coreshop.repository.order' => OrderRepositoryInterface::class,
                'coreshop.repository.payment' => PaymentRepositoryInterface::class,
            ],
        );
    }
}
