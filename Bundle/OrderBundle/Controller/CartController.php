<?php

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\PimcoreFrontendController;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CartController extends PimcoreFrontendController
{
    public function testAction()
    {
        $cart = $this->getCartManager()->getCart();
        $this->getCartManager()->persistCart($cart);

        return $this->render('@CoreShopFrontend/Cart/_widget.html.twig', [
            'cart' => $cart,
        ]);
    }

    public function cartPriceRuleAction(Request $request) {
        $code = $request->get('code');

        /**
         * 1. Find PriceRule for Code
         * 2. Check Validity
         * 3. Apply Price Rule to Cart
         */
        $voucherCode = $this->getCartPriceRuleVoucherRepository()->findByCode($code);

        if (!$voucherCode instanceof CartPriceRuleVoucherCodeInterface) {
            throw new NotFoundHttpException();
        }

        $priceRule = $voucherCode->getCartPriceRule();
        
        if ($this->getCartPriceRuleProcessor()->process($priceRule, $code, $this->getCartManager()->getCart())) {
            //SUCCESS
        }
        else {
            //NO SUCCESS
        }

        return $this->redirectToRoute('coreshop_shop_cart_summary');
    }

    protected function getCartPriceRuleProcessor() {
        return $this->get('coreshop.cart_price_rule.processor');
    }

    /**
     * @return CartPriceRuleVoucherRepositoryInterface
     */
    protected function getCartPriceRuleVoucherRepository() {
        return $this->get('coreshop.repository.cart_price_rule_voucher_code');
    }

    protected function getCartManager()
    {
        return $this->get('coreshop.cart.manager');
    }
}
