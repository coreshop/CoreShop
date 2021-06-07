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

use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManagerInterface;
use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\Checkout\CheckoutException;
use CoreShop\Component\Order\Checkout\CheckoutManagerFactoryInterface;
use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Order\Checkout\RedirectCheckoutStepInterface;
use CoreShop\Component\Order\Checkout\ValidationCheckoutStepInterface;
use CoreShop\Component\Order\CheckoutEvents;
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Order\Event\CheckoutEvent;
use CoreShop\Component\Order\OrderPaymentTransitions;
use CoreShop\Component\Order\OrderSaleTransitions;
use CoreShop\Component\Order\OrderTransitions;
use CoreShop\Component\Tracking\Tracker\TrackerInterface;
use Payum\Core\Payum;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

class CheckoutController extends FrontendController
{
    protected CheckoutManagerFactoryInterface $checkoutManagerFactory;

    public function __construct(CheckoutManagerFactoryInterface $checkoutManagerFactory)
    {
        $this->checkoutManagerFactory = $checkoutManagerFactory;
    }

    public function processAction(Request $request): Response
    {
        if (!$this->getCart()->hasItems()) {
            return $this->redirectToRoute('coreshop_cart_summary');
        }

        $checkoutManager = $this->checkoutManagerFactory->createCheckoutManager($this->getCart());

        /**
         * @var CheckoutStepInterface
         */
        $stepIdentifier = $request->get('stepIdentifier');
        $step = $checkoutManager->getStep($stepIdentifier);
        $dataForStep = [];
        $cart = $this->getCart();

        if (!$step instanceof CheckoutStepInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        //Check all previous steps if they are valid, if not, redirect back
        foreach ($checkoutManager->getPreviousSteps($stepIdentifier) as $previousStep) {
            if ($previousStep instanceof ValidationCheckoutStepInterface && !$previousStep->validate($cart)) {
                return $this->redirectToRoute('coreshop_checkout', ['stepIdentifier' => $previousStep->getIdentifier()]);
            }
        }

        $isValid = $step instanceof ValidationCheckoutStepInterface ? $step->validate($cart) : true;
        if ($isValid && $step->doAutoForward($cart)) {
            $nextStep = $checkoutManager->getNextStep($stepIdentifier);
            if ($nextStep) {
                return $this->redirectToRoute('coreshop_checkout', ['stepIdentifier' => $nextStep->getIdentifier()]);
            }
        }

        $event = new CheckoutEvent($this->getCart(), ['step' => $step, 'step_identifier', $stepIdentifier]);

        $this->get('event_dispatcher')->dispatch($event, CheckoutEvents::CHECKOUT_STEP_PRE);

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
                            $response = $this->redirectToRoute('coreshop_checkout', ['stepIdentifier' => $nextStep->getIdentifier()]);
                        }
                    }

                    //last step needs to tell us where to go!
                    if (!$checkoutManager->hasNextStep($stepIdentifier) && !$response instanceof Response) {
                        throw new \InvalidArgumentException(sprintf('Last step was executed, but no Response has been generated. To solve your issue, have a look at the last Checkout step %s and implement %s interface', $step->getIdentifier(), RedirectCheckoutStepInterface::class));
                    }

                    return $response;
                }
            } catch (CheckoutException $ex) {
                $dataForStep['exception'] = $ex->getTranslatableText();
            }
        }

        $isFirstStep = $checkoutManager->hasPreviousStep($stepIdentifier) === false;
        $this->get(TrackerInterface::class)->trackCheckoutStep($cart, $checkoutManager->getCurrentStepIndex($stepIdentifier), $isFirstStep);

        $preparedData = array_merge($dataForStep, $checkoutManager->prepareStep($step, $cart, $request));

        $dataForStep = array_merge($preparedData, [
            'cart' => $cart,
            'step' => $step,
            'identifier' => $stepIdentifier,
        ]);

        $event = new CheckoutEvent($this->getCart(), ['step' => $step, 'step_identifier', $stepIdentifier, 'step_params' => $dataForStep]);

        $this->get('event_dispatcher')->dispatch($event, CheckoutEvents::CHECKOUT_STEP_POST);

        if ($event->isStopped()) {
            $this->addEventFlash($event->getMessageType(), $event->getMessage(), $event->getMessageParameters());

            if ($event->hasResponse()) {
                return $event->getResponse();
            }

            return $this->redirectToRoute('coreshop_index');
        }

        return $this->renderResponseForCheckoutStep($request, $step, $stepIdentifier, $dataForStep);
    }

    protected function renderResponseForCheckoutStep(Request $request, CheckoutStepInterface $step, string $stepIdentifier, array $dataForStep): Response
    {
        $template = $this->templateConfigurator->findTemplate(sprintf('Checkout/steps/%s.html', $stepIdentifier));

        return $this->render($template, $dataForStep);
    }

    public function doCheckoutAction(Request $request): Response
    {
        $cart = $this->getCart();
        $checkoutManager = $this->checkoutManagerFactory->createCheckoutManager($cart);

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

            if ($step instanceof CheckoutStepInterface && $step instanceof ValidationCheckoutStepInterface && !$step->validate($this->getCart())) {
                return $this->redirectToRoute('coreshop_checkout', ['stepIdentifier' => $step->getIdentifier()]);
            }
        }

        $event = new CheckoutEvent($this->getCart());

        $this->get('event_dispatcher')->dispatch($event, CheckoutEvents::CHECKOUT_DO_PRE);

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
        $order = $this->getCart();

        $workflow = $this->get(StateMachineManagerInterface::class)->get($order, OrderSaleTransitions::IDENTIFIER);

        $workflow->apply($order, OrderSaleTransitions::TRANSITION_ORDER);

        $response = $this->redirectToRoute('coreshop_payment', ['order' => $order->getId()]);

        if (0 === $order->getTotal()) {
            $orderStateMachine = $this->get(StateMachineManagerInterface::class)->get($order, 'coreshop_order');
            $orderPaymentStateMachine = $this->get(StateMachineManagerInterface::class)->get($order, 'coreshop_order_payment');

            if ($orderStateMachine->can($order, OrderTransitions::TRANSITION_CONFIRM)) {
                $orderStateMachine->apply($order, OrderTransitions::TRANSITION_CONFIRM);
            }

            if ($orderPaymentStateMachine->can($order, OrderPaymentTransitions::TRANSITION_PAY)) {
                $orderPaymentStateMachine->apply($order, OrderPaymentTransitions::TRANSITION_PAY);
            }

            $request->getSession()->set('coreshop_order_id', $order->getId());

            $this->get('event_dispatcher')->dispatch(new CheckoutEvent($this->getCart(), ['order' => $order]), CheckoutEvents::CHECKOUT_DO_POST);

            $response = $this->redirectToRoute('coreshop_checkout_confirmation');
        }

        $event = new CheckoutEvent($this->getCart(), ['order' => $order]);

        $this->get('event_dispatcher')->dispatch($event, CheckoutEvents::CHECKOUT_DO_POST);

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

    public function errorAction(Request $request): Response
    {
        $orderId = $request->getSession()->get('coreshop_order_id', null);

        if (null === $orderId) {
            return $this->redirectToRoute('coreshop_index');
        }

        $request->getSession()->remove('coreshop_order_id');

        /**
         * @var OrderInterface $order
         */
        $order = $this->get('coreshop.repository.order')->find($orderId);
        Assert::notNull($order);

        $payments = $this->get('coreshop.repository.payment')->findForPayable($order);
        $lastPayment = is_array($payments) ? $payments[count($payments) - 1] : null;

        return $this->render($this->templateConfigurator->findTemplate('Checkout/error.html'), [
            'order' => $order,
            'payments' => $payments,
            'lastPayment' => $lastPayment,
        ]);
    }

    public function thankYouAction(Request $request): Response
    {
        $orderId = $request->getSession()->get('coreshop_order_id', null);

        if (null === $orderId) {
            return $this->redirectToRoute('coreshop_index');
        }

        $request->getSession()->remove('coreshop_order_id');
        $order = $this->get('coreshop.repository.order')->find($orderId);
        Assert::notNull($order);

        $this->get(TrackerInterface::class)->trackCheckoutComplete($order);

        //After successfull payment, we log out the customer
        if ($this->get(ShopperContextInterface::class)->hasCustomer() &&
            $this->get(ShopperContextInterface::class)->getCustomer()->getIsGuest()) {
            $this->get('security.token_storage')->setToken(null);
        }

        return $this->render($this->templateConfigurator->findTemplate('Checkout/thank-you.html'), [
            'order' => $order,
        ]);
    }

    protected function addEventFlash(string $type, string $message = null, array $parameters = []): void
    {
        if (!$message) {
            return;
        }

        if (!empty($parameters)) {
            $message = $this->prepareMessage($message, $parameters);
        }

        $this->addFlash($type, $message);
    }

    private function prepareMessage(string $message, array $parameters): array
    {
        return [
            'message' => $message,
            'parameters' => $parameters,
        ];
    }

    /**
     * @return \CoreShop\Component\Order\Model\OrderInterface
     */
    protected function getCart()
    {
        return $this->getCartContext()->getCart();
    }

    /**
     * @return CartContextInterface
     */
    protected function getCartContext()
    {
        return $this->get(CartContextInterface::class);
    }

    /**
     * @return Payum
     */
    protected function getPayum()
    {
        return $this->get('payum');
    }
}
