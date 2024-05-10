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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Order\Model\OrderItemInterface as BaseOrderItemInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use CoreShop\Component\Shipping\Model\ShippableItemInterface;

interface OrderItemInterface extends BaseOrderItemInterface, ShippableItemInterface
{
    public function getDigitalProduct(): ?bool;

    public function setDigitalProduct(?bool $digitalProduct);

    public function getUnitIdentifier(): ?string;

    public function setUnitIdentifier(?string $unitIdentifier);

    public function getUnitDefinition(): ?ProductUnitDefinitionInterface;

    public function setUnitDefinition(ProductUnitDefinitionInterface $productUnitDefinition);

    public function hasUnitDefinition(): bool;

    public function getDefaultUnitQuantity(): ?float;

    public function setDefaultUnitQuantity(?float $defaultUnitQuantity);

    public function getItemWeight(): ?float;

    public function setItemWeight(?float $itemWeight);

    public function getTotalWeight(): ?float;

    public function setTotalWeight(?float $totalWeight);

    public function getObjectId(): ?float;

    public function setObjectId(?float $objectId);

    public function getMainObjectId(): ?float;

    public function setMainObjectId(?float $objectId);
}
