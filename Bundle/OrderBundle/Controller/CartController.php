<?php

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\PimcoreFrontendController;

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

    protected function getCartManager()
    {
        return $this->get('coreshop.cart.manager');
    }
}
