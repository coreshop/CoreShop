<?php

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\PimcoreFrontendController;
use CoreShop\Component\Currency\Repository\CurrencyRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends PimcoreFrontendController
{
    public function menuAction(Request $request)
    {
        $categoryList = $this->repository->getListingClass();
        $categoryList->setLimit(5);

        return $this->render('CoreShopFrontendBundle:Category:_menu.html.twig', [
            'categories' => $categoryList->getObjects()
        ]);
    }
}
