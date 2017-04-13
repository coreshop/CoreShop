<?php

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Component\Core\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Order\Checkout\CheckoutManagerInterface;
use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class CheckoutController extends FrontendController
{
    /**
     * @var CheckoutManagerInterface
     */
    private $checkoutManager;

    /**
     * @param CheckoutManagerInterface $checkoutManager
     */
    public function __construct(CheckoutManagerInterface $checkoutManager)
    {
        $this->checkoutManager = $checkoutManager;
    }

    public function processAction(Request $request, $stepIdentifier)
    {
        /**
         * @var $step CheckoutStepInterface
         */
        $step = $this->checkoutManager->getStep($stepIdentifier);

        if (!$step instanceof CheckoutStepInterface) {
            return $this->redirectToRoute('coreshop_shop_index');
        }

        //Check all previous steps if they are valid, if not, redirect back
        foreach ($this->checkoutManager->getPreviousSteps($stepIdentifier) as $previousStep) {
            if (!$previousStep->validate($this->getCart())) {
                return $this->redirectToRoute('coreshop_shop_checkout', ['stepIdentifier' => $previousStep->getIdentifier()]);
            }
        }

        if ($step->validate($this->getCart()) && $step->doAutoForward()) {
            $nextStep = $this->checkoutManager->getNextStep($stepIdentifier);

            if ($nextStep) {
                return $this->redirectToRoute('coreshop_shop_checkout', ['stepIdentifier' => $nextStep->getIdentifier()]);
            }
        }

        if ($request->isMethod('POST')) {
            if ($step->commitStep($this->getCart(), $request)) {
                $nextStep = $this->checkoutManager->getNextStep($stepIdentifier);

                if ($nextStep) {
                    return $this->redirectToRoute('coreshop_shop_checkout', ['stepIdentifier' => $nextStep->getIdentifier()]);
                }
            }
        }

        $dataForStep = $step->prepareStep($this->getCart());

        $dataForStep = array_merge(is_array($dataForStep) ? $dataForStep : [], [
            'cart' => $this->getCart(),
            'checkoutSteps' => $this->checkoutManager->getSteps(),
            'currentStep' => $this->checkoutManager->getCurrentStepIndex($stepIdentifier),
            'step' => $step,
            'identifier' => $stepIdentifier
        ]);

        return $this->render(sprintf('@CoreShopFrontend/Checkout/steps/%s.html.twig', $stepIdentifier), $dataForStep);
    }

    public function doCheckoutAction(Request $request) {
        /**
         * after the last step, we come here
         *
         * what are we doing here?
         *  1. Create Order with Workflow State: initialized, pending-payment
         *  2. Use Payum and redirect to Payment Provider
         *  3. Return to PaymentController with afterCapture
         *  4. Payment Controller changes the state of the Order to "payment-done"?
         *  5. PaymentController redirects us back here to "thankYouAction"
         *
         *
         * therefore we need the CartToOrderTransformerInterface here
         */

        //Check all previous steps if they are valid, if not, redirect back
        /**
         * @var $step CheckoutStepInterface
         */
        foreach ($this->checkoutManager->getSteps() as $stepIdentifier) {
            $step = $this->checkoutManager->getStep($stepIdentifier);

            if (!$step->validate($this->getCart())) {
                return $this->redirectToRoute('coreshop_shop_checkout', ['stepIdentifier' => $step->getIdentifier()]);
            }
        }

        /**
         * If everything is valid, we continue with Order-Creation
         */
        $order = $this->getOrderFactory()->createNew();
        $order = $this->getCartToOrderTransformer()->transform($this->getCart(), $order);

        var_dump($order->getId());

        exit;
    }

    /**
     * @return \CoreShop\Component\Order\Model\CartInterface
     */
    private function getCart()
    {
        return $this->getCartManager()->getCart();
    }

    /**
     * @return \CoreShop\Bundle\OrderBundle\Manager\CartManager
     */
    private function getCartManager()
    {
        return $this->get('coreshop.cart.manager');
    }

    /**
     * @return \CoreShop\Component\Core\Transformer\CartToOrderTransformer
     */
    private function getCartToOrderTransformer() {
        return $this->get('coreshop.order.transformer.cart_to_order');
    }

    /**
     * @return \CoreShop\Component\Resource\Factory\PimcoreFactory
     */
    private function getOrderFactory() {
        return $this->get('coreshop.factory.order');
    }
}
