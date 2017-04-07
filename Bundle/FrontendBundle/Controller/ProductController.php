<?php

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Component\Currency\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Product\Repository\ProductRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends FrontendController
{
    public function latestAction(Request $request)
    {
        /**
         * @var $productRepository ProductRepositoryInterface
         */
        $productRepository = $this->get('coreshop.repository.product');
        $storeRepository = $this->get('coreshop.repository.store');

        return $this->render('CoreShopFrontendBundle:Product:_latest.html.twig', [
            'products' => $productRepository->getLatestByShop($storeRepository->find(1))
        ]);
    }
}
