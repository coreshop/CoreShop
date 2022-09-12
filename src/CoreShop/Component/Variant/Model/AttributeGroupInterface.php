<?php
declare(strict_types=1);

namespace CoreShop\Component\Variant\Model;

use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

interface AttributeGroupInterface extends PimcoreModelInterface
{
    public function getName(string $language = null): ?string;

    public function setName(?string $name, $language = null);

    public function getSorting(): ?float;

    public function setSorting(?float $sorting);

    public function getShowInList(): ?bool;

    public function setShowInList(?bool $showInList);
}
