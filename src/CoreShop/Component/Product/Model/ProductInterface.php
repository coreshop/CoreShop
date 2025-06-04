<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Pimcore\Slug\KeyableSluggableInterface;
use CoreShop\Component\Pimcore\Slug\SluggableInterface;
use CoreShop\Component\Resource\Model\ToggleableInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use Pimcore\Model\Asset\Image;

interface ProductInterface extends PimcoreModelInterface, ToggleableInterface, SluggableInterface, KeyableSluggableInterface
{
    public function getId(): ?int;

    public function getSku(): ?string;

    public function setSku(?string $sku);

    public function getName(?string $language = null): ?string;

    public function setName(?string $name, ?string $language = null);

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

    public function getShortDescription(?string $language = null): ?string;

    public function setShortDescription(?string $shortDescription, ?string $language = null);

    public function getDescription(?string $language = null): ?string;

    public function setDescription(?string $description, ?string $language = null);

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
