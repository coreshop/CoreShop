<?php
declare(strict_types=1);

namespace CoreShop\Component\Variant\Model;

use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use Pimcore\Model\DataObject\Concrete;

interface ProductVariantAwareInterface extends PimcoreModelInterface
{
    public function getName(string $language = null): ?string;

    public function setName(?string $name, $language = null);

    /**
     * @return AttributeInterface[]|null
     */
    public function getAttributes(): ?array;

    public function setAttributes(array $attributes);

    /**
     * @return AttributeGroupInterface[]|null
     */
    public function getAllowedAttributeGroups(): ?array;

    public function setAllowedAttributeGroups(array $allowedGroups);

    public function findAttributeForGroup(AttributeGroupInterface $attributeGroup): ?AttributeInterface;

    public function findMainVariant(): ?ProductVariantAwareInterface;

    public function getMainVariant(): ?Concrete;

    public function setMainVariant(?Concrete $purchasable);

    public function getVariantParent();

    public function getVariants(): array;
}
