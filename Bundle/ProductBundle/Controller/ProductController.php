<?php

namespace CoreShop\Bundle\ProductBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\PimcoreResourceController;
use CoreShop\Component\Product\Model\ProductPriceRule;
use CoreShop\Component\Product\Pimcore\Model\ProductInterface;
use CoreShop\Component\Rule\Model\Action;
use CoreShop\Component\Rule\Model\Condition;

class ProductController extends PimcoreResourceController {

    public function testAction() {
        /**
         * @var $product ProductInterface
         */
        $product = $this->repository->find(65);

        echo $product->getPrice();

        $condition1 = new Condition();
        $condition1->setType('quantity');
        $condition1->setConfiguration(['amount' => 10]);

        $action1 = new Action();
        $action1->setType('new_price');
        $action1->setConfiguration(['price' => 1000]);

        $rule = new ProductPriceRule();
        $rule->addCondition($condition1);
        $rule->addAction($action1);

        exit;
    }

}