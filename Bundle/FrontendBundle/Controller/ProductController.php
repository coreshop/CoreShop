<?php

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\PimcoreFrontendController;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends PimcoreFrontendController
{
    public function latestAction(Request $request)
    {
        $storeRepository = $this->get('coreshop.repository.store');

        return $this->render('CoreShopFrontendBundle:Product:_latest.html.twig', [
            'products' => $this->repository->getLatestByShop($storeRepository->find(1))
        ]);
    }
}
