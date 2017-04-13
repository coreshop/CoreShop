<?php

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Component\Core\Pimcore\ObjectServiceInterface;
use CoreShop\Component\Core\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Order\Checkout\CheckoutManagerInterface;
use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Resource\Factory\PimcoreFactory;
use CoreShop\Component\Resource\Factory\PimcoreFactoryInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use Payum\Core\Payum;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

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

    /**
     * @param Request $request
     * @param $stepIdentifier
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
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
         *  1. Create Order with Workflow State: initialized
         *  2. Use Payum and redirect to Payment Provider
         *  3. PayumBundle takes care about payment stuff
         *  4. After Payment is done, we return to PayumBundle PaymentController and further process it
         *
         * therefore we need the CartToOrderTransformerInterface here
         */

        /**
         * Before we do anything else, lets check if the checkout is still valid
         * Check all previous steps if they are valid, if not, redirect back
         */
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

        return $this->redirectToRoute('coreshop_shop_payment', ['orderId' => $order->getId()]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function thankYouAction(Request $request)
    {
        $orderId = $request->getSession()->get('coreshop_order_id', null);

        if (null === $orderId) {
            return $this->redirectToRoute('coreshop_shop_index');
        }

        $request->getSession()->remove('coreshop_order_id');
        $order = $this->get('coreshop.repository.order')->find($orderId);
        Assert::notNull($order);

        return $this->render('@CoreShopFrontend/Checkout/thank-you.html.twig', [
            'order' => $order
        ]);
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

    /**
     * @return Payum
     */
    protected function getPayum()
    {
        return $this->get('payum');
    }
}
