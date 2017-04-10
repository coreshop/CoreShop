<?php

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\PimcoreFrontendController;
use CoreShop\Component\Product\Model\ProductInterface;
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

    public function detailAction(Request $request, $name, $productId) {
        $product = $this->repository->find($productId);

        if (!$product instanceof ProductInterface) {
            return $this->redirectToRoute('coreshop_shop_index');
        }

        return $this->render('CoreShopFrontendBundle:Product:detail.html.twig', [
            'product' => $product
        ]);
    }
}
