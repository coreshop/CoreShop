<?php

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Component\Currency\Repository\CurrencyRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

class CartController extends FrontendController
{
    public function widgetAction(Request $request)
    {
        $cartManager = $this->get('coreshop.cart.manager');
        $cart = $cartManager->getCart();

        if ( $this->get('templating')->exists('CoreShopFrontendBundle:Cart:_widget.html.twig') ) {

        }

        //@AppBundle:Sola

        return $this->render('CoreShopFrontendBundle:Cart:_widget.html.twig', [
            'cart' => $cart
        ]);
    }
}
