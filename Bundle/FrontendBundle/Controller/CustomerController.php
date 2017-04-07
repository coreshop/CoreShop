<?php

namespace CoreShop\Bundle\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class CustomerController extends FrontendController
{
    public function headerAction(Request $request)
    {
        return $this->render('CoreShopFrontendBundle:Customer:header.html.twig', [
            'catalogMode' => false,
            'customer' => null
        ]);
    }

    public function footerAction() {
        return $this->render('CoreShopFrontendBundle:Customer:_footer.html.twig', [
            'catalogMode' => false,
            'customer' => null
        ]);
    }
}
