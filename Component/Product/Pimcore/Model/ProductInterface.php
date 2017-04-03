<?php

namespace CoreShop\Component\Product\Pimcore\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Taxation\Model\TaxRuleInterface;
use Pimcore\Model\Element\ElementInterface;

interface ProductInterface extends ResourceInterface, ElementInterface
{
    /**
     * @param null $language
     * @return mixed
     */
    public function getName($language = null);

    /**
     * @param $name
     * @param null $language
     * @return mixed
     */
    public function setName($name, $language = null);

    /**
     * @return mixed
     */
    public function getPrice();

    /**
     * @return mixed
     */
    public function getBasePrice();

    /**
     * @param $basePrice
     * @return mixed
     */
    public function setBasePrice($basePrice);

    /**
     * @return mixed
     */
    public function getAvailableForOrder();

    /**
     * @param $availableForOrder
     * @return mixed
     */
    public function setAvailableForOrder($availableForOrder);

    /**
     * @return TaxRuleInterface
     */
    public function getTaxRule();

    /**
     * @param TaxRuleInterface $taxRule
     * @return mixed
     */
    public function setTaxRule($taxRule);
}