<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\Model\ToggleableInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use Pimcore\Model\Asset\Image;

interface ProductInterface extends PimcoreModelInterface, ToggleableInterface
{
    public function getSku(): ?string;

    public function setSku(?string $sku);

    public function getName($language = null): ?string;

    public function setName(?string $name, $language = null);

    public function getItemQuantityFactor(): ?int;

    public function setItemQuantityFactor(?int $itemQuantityFactor);

    /**
     * @return CategoryInterface[]
     */
    public function getCategories(): ?array;

    /**
     * @param CategoryInterface[] $categories
     */
    public function setCategories(array $categories);

    public function getImage(): ?Image;

    public function getImages(): array;

    public function setImages(array $images);

    public function getManufacturer(): ?ManufacturerInterface;

    public function setManufacturer(?ManufacturerInterface $manufacturer);

    public function getEan(): ?string;

    public function setEan(?string $ean);

    public function getShortDescription($language = null): ?string;

    public function setShortDescription(?string $shortDescription, $language = null);

    public function getDescription($language = null): ?string;

    public function setDescription(?string $description, $language = null);

    public function getWeight(): ?float;

    public function setWeight(?float $weight);

    public function getWidth(): ?float;

    public function setWidth(?float $width);

    public function getHeight(): ?float;

    public function setHeight(?float $height);

    public function getDepth(): ?float;

    public function setDepth(?float $depth);

    /**
     * @return PriceRuleInterface[]
     */
    public function getSpecificPriceRules(): array;

    /**
     * @param PriceRuleInterface[] $specificPriceRules
     */
    public function setSpecificPriceRules(array $specificPriceRules);

    public function getUnitDefinitions(): ?ProductUnitDefinitionsInterface;

    public function setUnitDefinitions(ProductUnitDefinitionsInterface $productUnitDefinitions);

    public function hasUnitDefinitions(): bool;

    public function hasDefaultUnitDefinition(): bool;

    public function hasAdditionalUnitDefinitions(): bool;
}
