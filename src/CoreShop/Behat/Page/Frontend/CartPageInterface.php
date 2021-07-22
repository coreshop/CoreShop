<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Behat\Page\Frontend;

use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;

interface CartPageInterface extends FrontendPageInterface
{
    public function isEmpty(): bool;

    public function isSingleItemOnPage(): bool;

    public function hasItemNamed(string $name): bool;

    public function hasProductInUnit(string $name, ProductUnitDefinitionInterface $unitDefinition): bool;

    public function getItemUnitPriceWithUnit(string $name, ProductUnitDefinitionInterface $unitDefinition): string;

    public function getItemUnitPrice(string $productName): string;

    public function getItemTotalPrice(string $productName): string;

    public function getItemTotalPriceWithUnit(string $name, ProductUnitDefinitionInterface $unitDefinition): string;

    public function getQuantity(string $productName): int;

    public function changeQuantity(string $productName, string $quantity): void;

    public function removeProduct(string $productName): void;

    public function applyVoucherCode(string $voucherCode): void;

    public function getTotal(): string;
}
