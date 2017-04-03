<?php

namespace CoreShop\Component\Product\Pimcore\Model;

use CoreShop\Bundle\CoreBundle\ImplementedByPimcoreException;
use CoreShop\Component\Product\Calculator\ProductPriceRuleCalculatorInterface;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;
use CoreShop\Component\Rule\Model\RuleInterface;
use CoreShop\Component\Rule\Model\RuleSubjectInterface;

class Product extends AbstractPimcoreModel implements RuleSubjectInterface, ProductInterface {

    /**
     * {@inheritdoc}
     */
    public function getPrice() {
        /**
         * @var $calculator ProductPriceRuleCalculatorInterface
         */
        $calculator = $this->container->get('coreshop.product.price_calculator');

        return $calculator->getPrice($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getRules() {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function hasRule(RuleInterface $rule) {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function addRule(RuleInterface $rule) {

    }

    /**
     * {@inheritdoc}
     */
    public function removeRule(RuleInterface $rule) {

    }

    /**
     * {@inheritdoc}
     */
    public function getName($language = null)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name, $language = null)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getBasePrice()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setBasePrice($basePrice)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableForOrder()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setAvailableForOrder($availableForOrder)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxRule()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxRule($taxRule)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }
}