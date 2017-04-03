<?php

namespace CoreShop\Bundle\ProductBundle\Controller;

use CoreShop\Bundle\ProductBundle\Pimcore\Model\Product;
use CoreShop\Bundle\ResourceBundle\Controller\PimcoreResourceController;
use CoreShop\Component\Product\Pimcore\Model\ProductInterface;

class ProductController extends PimcoreResourceController {

    public function testAction() {
        /**
         * @var $product ProductInterface
         */
        $product = $this->repository->find(65);
        
        echo $product->getPrice();

        exit;
    }

}