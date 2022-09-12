<?php
declare(strict_types=1);

namespace CoreShop\Component\Variant\Model;

use Pimcore\Model\DataObject\Data\RgbaColor;

interface AttributeColorInterface extends AttributeInterface
{
    public function getValueColor(): ?RgbaColor;

    public function setValueColor(?RgbaColor $color);
}
