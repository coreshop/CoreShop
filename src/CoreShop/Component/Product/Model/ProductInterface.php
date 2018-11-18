<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\Model\ToggleableInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

interface ProductInterface extends PimcoreModelInterface, ToggleableInterface
{
    /**
     * @return string
     */
    public function getSku();

    /**
     * @param string $sku
     */
    public function setSku($sku);

    /**
     * @param string $language
     *
     * @return string
     */
    public function getName($language = null);

    /**
     * @param string $name
     * @param string $language
     */
    public function setName($name, $language = null);

    /**
     * @return int
     */
    public function getWholesalePrice();

    /**
     * @param int $wholesalePrice
     */
    public function setWholesalePrice($wholesalePrice);

    /**
     * @return CategoryInterface[]
     */
    public function getCategories();

    /**
     * @param CategoryInterface[] $categories
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
     * @param mixed $images
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
     * @return ProductInterface
     */
    public function getVariantMaster();

    /**
     * Get all Variants Differences.
     *
     * @param string $language
     * @param string $type
     * @param string $field
     *
     * @return array|bool
     */
    public function getVariantDifferences($language, $type = 'objectbricks', $field = 'variants');

    /**
     * @param string $language
     *
     * @return string
     */
    public function getShortDescription($language = null);

    /**
     * @param string $shortDescription
     * @param string|null $language
     */
    public function setShortDescription($shortDescription, $language = null);

    /**
     * @param string|null $language
     *
     * @return string
     */
    public function getDescription($language = null);

    /**
     * @param string $description
     * @param string|null $language
     */
    public function setDescription($description, $language = null);

    /**
     * @return float
     */
    public function getWeight();

    /**
     * @param float $weight
     */
    public function setWeight($weight);

    /**
     * @return float
     */
    public function getWidth();

    /**
     * @param float $width
     */
    public function setWidth($width);

    /**
     * @return float
     */
    public function getHeight();

    /**
     * @param float $height
     */
    public function setHeight($height);

    /**
     * @return float
     */
    public function getDepth();

    /**
     * @param float $depth
     */
    public function setDepth($depth);

    /**
     * @return PriceRuleInterface[]
     */
    public function getSpecificPriceRules();

    /**
     * @param PriceRuleInterface[] $specificPriceRules
     */
    public function setSpecificPriceRules($specificPriceRules);
}
