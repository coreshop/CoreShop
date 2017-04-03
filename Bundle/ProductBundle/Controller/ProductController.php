<?php

namespace CoreShop\Bundle\ProductBundle\Controller;

use CoreShop\Bundle\ProductBundle\Rule\Checker\QuantityConditionChecker;
use CoreShop\Bundle\ResourceBundle\Controller\PimcoreResourceController;
use CoreShop\Component\Product\Model\ProductPriceRule;
use CoreShop\Component\Product\Pimcore\Model\ProductInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;
use CoreShop\Component\Rule\Model\Condition;

class ProductController extends PimcoreResourceController {

    public function testAction() {
        /**
         * @var $product ProductInterface
         */
        $product = $this->repository->find(65);

        $condition1 = new Condition();
        $condition1->setType('quantity');
        $condition1->setConfiguration(['amount' => 10]);

        $rule = new ProductPriceRule();
        $rule->addCondition($condition1);

        /**
         * @var $ruleProcessor RuleValidationProcessorInterface
         */
        $ruleProcessor = $this->container->get('coreshop.product_price_rule.processor');

        $ruleProcessor->isValid($product, $rule);;

        exit;
    }

}