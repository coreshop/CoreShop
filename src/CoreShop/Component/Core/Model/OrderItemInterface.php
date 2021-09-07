<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

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
}
