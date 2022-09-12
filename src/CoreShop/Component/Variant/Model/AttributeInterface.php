<?php
declare(strict_types=1);

namespace CoreShop\Component\Variant\Model;

use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

interface AttributeInterface extends PimcoreModelInterface
{
    public function getName(string $language = null): ?string;

    public function setName(?string $name, $language = null);

    public function getValueText(): ?string;

    public function setValueText(?string $valueText);

    public function getAttributeGroup(): ?AttributeGroupInterface;

    public function setAttributeGroup(?AttributeGroupInterface $attributeGroup);

    public function getSorting(): ?float;

    public function setSorting(?float $sorting);
}
