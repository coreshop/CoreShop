<?php

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\TaxRuleGroupInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Model\TaxRuleInterface;

/**
 * @todo: lot of stuff, including images
 */
interface ProductInterface extends PimcoreModelInterface
{
    /**
     * @param null $language
     *
     * @return mixed
     */
    public function getName($language = null);

    /**
     * @param $name
     * @param null $language
     *
     * @return mixed
     */
    public function setName($name, $language = null);

    /**
     * @param bool $withTax
     *
     * @return float
     */
    public function getPrice($withTax = true);

    /**
     * @param bool $withTax
     *
     * @return mixed
     */
    public function getBasePrice($withTax = true);

    /**
     * @param $basePrice
     *
     * @return mixed
     */
    public function setBasePrice($basePrice);

    /**
     * @return mixed
     */
    public function getWholesalePrice();

    /**
     * @param $wholesalePrice
     *
     * @return mixed
     */
    public function setWholesalePrice($wholesalePrice);

    /**
     * @return mixed
     */
    public function getAvailableForOrder();

    /**
     * @param $availableForOrder
     *
     * @return mixed
     */
    public function setAvailableForOrder($availableForOrder);

    /**
     * @return TaxRuleInterface
     */
    public function getTaxRule();

    /**
     * @param TaxRuleGroupInterface $taxRule
     *
     * @return mixed
     */
    public function setTaxRule($taxRule);

    /**
     * @param AddressInterface|null $address
     * @return TaxCalculatorInterface
     */
    public function getTaxCalculator(AddressInterface $address = null);

    /**
     * @return CategoryInterface[]
     */
    public function getCategories();

    /**
     * @param CategoryInterface[] $categories
     * @return mixed
     */
    public function setCategories($categories);

    /**
     * @return mixed
     */
    public function getImage();

    /**
     * @return mixed
     */
    public function getImages();

    /**
     * @param $images
     */
    public function setImages($images);

    /**
     * @return ManufacturerInterface
     */
    public function getManufacturer();

    /**
     * @param ManufacturerInterface $manufacturer
     */
    public function setManufacturer($manufacturer);

    /**
     * @return string
     */
    public function getEan();

    /**
     * @param string $ean
     */
    public function setEan($ean);

    /**
     * @return int
     */
    public function getQuantity();

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity);

    /**
     * @return boolean
     */
    public function getIsAvailableWhenOutOfStock();

    /**
     * @param boolean $isAvailableWhenOutOfStock
     */
    public function setIsAvailableWhenOutOfStock($isAvailableWhenOutOfStock);

    /**
     * Get all Variants Differences.
     *
     * @param $language
     * @param $type
     * @param $field
     *
     * @return array|boolean
     */
    public function getVariantDifferences($language, $type = 'objectbricks', $field = 'variants');

    /**
     * @return float
     */
    public function getTaxRate();

    /**
     * Get Product Tax Amount.
     *
     * @return float
     */
    public function getTaxAmount();

    /**
     * @param string $language
     * @return string
     */
    public function getShortDescription($language = null);

    /**
     * @param string $shortDescription
     * @param string $language
     */
    public function setShortDescription($shortDescription, $language = null);

    /**
     * @param string $language
     * @return string
     */
    public function getDescription($language = null);

    /**
     * @param string $description
     * @param string $language
     */
    public function setDescription($description, $language = null);
}
