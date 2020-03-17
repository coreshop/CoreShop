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

use CoreShop\Bundle\FrontendBundle\TemplateConfigurator\TemplateConfiguratorInterface;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManagerInterface;
use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\Checkout\CheckoutException;
use CoreShop\Component\Order\Checkout\CheckoutManagerFactoryInterface;
use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Order\Checkout\RedirectCheckoutStepInterface;
use CoreShop\Component\Order\Checkout\ValidationCheckoutStepInterface;
use CoreShop\Component\Order\CheckoutEvents;
use CoreShop\Component\Order\Event\CheckoutEvent;
use CoreShop\Component\Order\OrderSaleTransitions;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\Payment\Repository\PaymentRepositoryInterface;
use CoreShop\Component\Tracking\Tracker\TrackerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Webmozart\Assert\Assert;

class CheckoutController extends FrontendController
{
    public function processAction(
        Request $request,
        ShopperContextInterface $shopperContext,
        CheckoutManagerFactoryInterface $checkoutManagerFactory,
        EventDispatcherInterface $eventDispatcher,
        TrackerInterface $tracker,
        TemplateConfiguratorInterface $templateConfigurator
    ): Response {
        if (!$shopperContext->getCart()->hasItems()) {
            return $this->redirectToRoute('coreshop_cart_summary');
        }

        $checkoutManager = $checkoutManagerFactory->createCheckoutManager($shopperContext->getCart());

        /**
         * @var CheckoutStepInterface
         */
        $stepIdentifier = $request->get('stepIdentifier');
        $step = $checkoutManager->getStep($stepIdentifier);
        $dataForStep = [];
        $cart = $shopperContext->getCart();

        if (!$step instanceof CheckoutStepInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        //Check all previous steps if they are valid, if not, redirect back
        foreach ($checkoutManager->getPreviousSteps($stepIdentifier) as $previousStep) {
            if ($previousStep instanceof ValidationCheckoutStepInterface && !$previousStep->validate($cart)) {
                return $this->redirectToRoute('coreshop_checkout',
                    ['stepIdentifier' => $previousStep->getIdentifier()]);
            }
        }

        $isValid = $step instanceof ValidationCheckoutStepInterface ? $step->validate($cart) : true;
        if ($isValid && $step->doAutoForward($cart)) {
            $nextStep = $checkoutManager->getNextStep($stepIdentifier);
            if ($nextStep) {
                return $this->redirectToRoute('coreshop_checkout', ['stepIdentifier' => $nextStep->getIdentifier()]);
            }
        }

        $event = new CheckoutEvent($shopperContext->getCart(), ['step' => $step, 'step_identifier', $stepIdentifier]);

        $eventDispatcher->dispatch(CheckoutEvents::CHECKOUT_STEP_PRE, $event);

        if ($event->isStopped()) {
            $this->addEventFlash($event->getMessageType(), $event->getMessage(), $event->getMessageParameters());

            if ($event->hasResponse()) {
                return $event->getResponse();
            }

            return $this->redirectToRoute('coreshop_index');
        }

        if ($request->isMethod('POST')) {
            try {
                if ($step->commitStep($cart, $request)) {
                    $response = null;

                    if ($step instanceof RedirectCheckoutStepInterface) {
                        $response = $step->getResponse($cart, $request);
                    } else {
                        $nextStep = $checkoutManager->getNextStep($stepIdentifier);

                        if ($nextStep) {
                            $response = $this->redirectToRoute('coreshop_checkout',
                                ['stepIdentifier' => $nextStep->getIdentifier()]);
                        }
                    }

                    //last step needs to tell us where to go!
                    if (!$checkoutManager->hasNextStep($stepIdentifier) && !$response instanceof Response) {
                        throw new \InvalidArgumentException(sprintf('Last step was executed, but no Response has been generated. To solve your issue, have a look at the last Checkout step %s and implement %s interface',
                            $step->getIdentifier(), RedirectCheckoutStepInterface::class));
                    }

                    return $response;
                }
            } catch (CheckoutException $ex) {
                $dataForStep['exception'] = $ex->getTranslatableText();
            }
        }

        $isFirstStep = $checkoutManager->hasPreviousStep($stepIdentifier) === false;
        $tracker->trackCheckoutStep($cart, $checkoutManager->getCurrentStepIndex($stepIdentifier), $isFirstStep);

        $preparedData = array_merge($dataForStep, $checkoutManager->prepareStep($step, $cart, $request));

        $dataForStep = array_merge($preparedData, [
            'cart' => $cart,
            'step' => $step,
            'identifier' => $stepIdentifier,
        ]);

        $event = new CheckoutEvent($shopperContext->getCart(),
            ['step' => $step, 'step_identifier', $stepIdentifier, 'step_params' => $dataForStep]);

        $eventDispatcher->dispatch(CheckoutEvents::CHECKOUT_STEP_POST, $event);

        if ($event->isStopped()) {
            $this->addEventFlash($event->getMessageType(), $event->getMessage(), $event->getMessageParameters());

            if ($event->hasResponse()) {
                return $event->getResponse();
            }

            return $this->redirectToRoute('coreshop_index');
        }

        return $this->renderResponseForCheckoutStep($request, $templateConfigurator, $step, $stepIdentifier,
            $dataForStep);
    }

    protected function renderResponseForCheckoutStep(
        Request $request,
        TemplateConfiguratorInterface $templateConfigurator,
        CheckoutStepInterface $step,
        $stepIdentifier,
        $dataForStep
    ): Response {
        $template = $templateConfigurator->findTemplate(sprintf('Checkout/steps/%s.html', $stepIdentifier));

        return $this->renderTemplate($template, $dataForStep);
    }

    public function doCheckoutAction(
        Request $request,
        ShopperContextInterface $shopperContext,
        CheckoutManagerFactoryInterface $checkoutManagerFactory,
        EventDispatcherInterface $eventDispatcher,
        StateMachineManagerInterface $stateMachineManager
    ): Response {
        $cart = $shopperContext->getCart();
        $checkoutManager = $checkoutManagerFactory->createCheckoutManager($cart);

        /*
         * after the last step, we come here
         *
         * what are we doing here?
         *  1. Commit Order (eg. change state)
         *  2. Use Payum and redirect to Payment Provider
         *  3. PayumBundle takes care about payment stuff
         *  4. After Payment is done, we return to PayumBundle PaymentController and further process it
         *
         * therefore we need the CartToOrderTransformerInterface here
        */

        /*
         * Before we do anything else, lets check if the checkout is still valid
         * Check all previous steps if they are valid, if not, redirect back
         */

        foreach ($checkoutManager->getSteps() as $stepIdentifier) {
            /**
             * @var CheckoutStepInterface $step
             */
            $step = $checkoutManager->getStep($stepIdentifier);

            if ($step instanceof CheckoutStepInterface && $step instanceof ValidationCheckoutStepInterface && !$step->validate($shopperContext->getCart())) {
                return $this->redirectToRoute('coreshop_checkout', ['stepIdentifier' => $step->getIdentifier()]);
            }
        }

        $event = new CheckoutEvent($shopperContext->getCart());

        $eventDispatcher->dispatch(CheckoutEvents::CHECKOUT_DO_PRE, $event);

        if ($event->isStopped()) {
            $this->addEventFlash($event->getMessageType(), $event->getMessage(), $event->getMessageParameters());

            if ($event->hasResponse()) {
                return $event->getResponse();
            }

            return $this->redirectToRoute('coreshop_index');
        }

        /**
         * If everything is valid, we continue with Order-Creation.
         */
        $order = $shopperContext->getCart();

        $workflow = $stateMachineManager->get($order, OrderSaleTransitions::IDENTIFIER);

        $workflow->apply($order, OrderSaleTransitions::TRANSITION_ORDER);

        $response = $this->redirectToRoute('coreshop_payment', ['order' => $order->getId()]);

        if (0 === $order->getTotal()) {
            $request->getSession()->set('coreshop_order_id', $order->getId());

            $eventDispatcher->dispatch(
                CheckoutEvents::CHECKOUT_DO_POST,
                new CheckoutEvent($shopperContext->getCart(), ['order' => $order])
            );

            $response = $this->redirectToRoute('coreshop_checkout_confirmation');
        }

        $event = new CheckoutEvent($shopperContext->getCart(), ['order' => $order]);

        $eventDispatcher->dispatch(CheckoutEvents::CHECKOUT_DO_POST, $event);

        if ($event->isStopped()) {
            $this->addEventFlash($event->getMessageType(), $event->getMessage(), $event->getMessageParameters());

            if ($event->hasResponse()) {
                return $event->getResponse();
            }

            return $this->redirectToRoute('coreshop_index');
        }

        /*
         * TODO: Not sure if we should create payment object right here, if so, the PaymentBundle would'nt be responsible for it :/
        */

        return $response;
    }

    public function errorAction(
        Request $request,
        OrderRepositoryInterface $orderRepository,
        PaymentRepositoryInterface $paymentRepository,
        TemplateConfiguratorInterface $templateConfigurator
    ): Response {
        $orderId = $request->getSession()->get('coreshop_order_id', null);

        if (null === $orderId) {
            return $this->redirectToRoute('coreshop_index');
        }

        $request->getSession()->remove('coreshop_order_id');

        /**
         * @var OrderInterface $order
         */
        $order = $orderRepository->find($orderId);
        Assert::notNull($order);

        $payments = $paymentRepository->findForPayable($order);
        $lastPayment = is_array($payments) ? $payments[count($payments) - 1] : null;

        return $this->renderTemplate($templateConfigurator->findTemplate('Checkout/error.html'), [
            'order' => $order,
            'payments' => $payments,
            'lastPayment' => $lastPayment,
        ]);
    }

    public function thankYouAction(
        Request $request,
        OrderRepositoryInterface $orderRepository,
        TrackerInterface $tracker,
        ShopperContextInterface $shopperContext,
        TokenStorageInterface $tokenStorage,
        TemplateConfiguratorInterface $templateConfigurator
    ): Response
    {
        $orderId = $request->getSession()->get('coreshop_order_id', null);

        if (null === $orderId) {
            return $this->redirectToRoute('coreshop_index');
        }

        $request->getSession()->remove('coreshop_order_id');
        $order = $orderRepository->find($orderId);
        Assert::notNull($order);

        $tracker->trackCheckoutComplete($order);

        //After successfull payment, we log out the customer
        if ($shopperContext->hasCustomer() && $shopperContext->getCustomer()->getIsGuest()) {
            $tokenStorage->setToken(null);
        }

        return $this->renderTemplate($templateConfigurator->findTemplate('Checkout/thank-you.html'), [
            'order' => $order,
        ]);
    }

    protected function addEventFlash(string $type, string $message = null, array $parameters = [])
    {
        if (!$message) {
            return;
        }

        if (!empty($parameters)) {
            $message = $this->prepareMessage($message, $parameters);
        }

        $this->addFlash($type, $message);
    }

    private function prepareMessage(string $message, array $parameters)
    {
        return [
            'message' => $message,
            'parameters' => $parameters,
        ];
    }
}
