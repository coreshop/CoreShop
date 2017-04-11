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
}
