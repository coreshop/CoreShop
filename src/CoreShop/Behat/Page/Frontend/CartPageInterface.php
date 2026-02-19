<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Behat\Page\Frontend;

use CoreShop\Bundle\TestBundle\Page\Frontend\FrontendPageInterface;
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
