<?php

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Component\Currency\Repository\CurrencyRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends FrontendController
{
    public function menuAction(Request $request)
    {
        $categoryList = $this->get('coreshop.repository.category')->getListingClass();
        $categoryList->setLimit(5);

        return $this->render('CoreShopFrontendBundle:Category:_menu.html.twig', [
            'categories' => $categoryList->getObjects()
        ]);
    }
}
