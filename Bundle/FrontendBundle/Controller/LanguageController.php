<?php

namespace CoreShop\Bundle\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class LanguageController extends FrontendController
{
    public function widgetAction(Request $request)
    {
        return $this->render('CoreShopFrontendBundle:Language:_widget.html.twig', [
            'languages' => ['de', 'en']//$this->get('pimcore.locale')->getLocaleList()
        ]);
    }
}
