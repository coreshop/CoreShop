<?php

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use CoreShop\Component\Taxation\Model\TaxRuleInterface;
use Pimcore\Model\Element\ElementInterface;

interface ProductInterface extends PimcoreModelInterface
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
     * @param bool $withTax
     * @return float
     */
    public function getPrice($withTax = true);

    /**
     * @param bool $withTax
     * @return mixed
     */
    public function getBasePrice($withTax = true);

    /**
     * @param $basePrice
     * @return mixed
     */
    public function setBasePrice($basePrice);

    /**
     * @return mixed
     */
    public function getWholesalePrice();

    /**
     * @param $wholesalePrice
     * @return mixed
     */
    public function setWholesalePrice($wholesalePrice);

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