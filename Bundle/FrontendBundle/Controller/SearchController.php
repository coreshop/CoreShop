<?php

namespace CoreShop\Bundle\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class SearchController extends FrontendController
{
    public function widgetAction(Request $request)
    {
        return $this->render('CoreShopFrontendBundle:Search:_widget.html.twig', [
            //TODO: FORM
        ]);
    }
}
