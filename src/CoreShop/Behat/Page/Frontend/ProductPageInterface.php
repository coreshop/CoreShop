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

namespace CoreShop\Behat\Page\Frontend;

use Behat\Mink\Exception\ElementNotFoundException;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use CoreShop\Component\Product\Model\ProductUnitInterface;

interface ProductPageInterface extends FrontendPageInterface
{
    public function getContent(): string;

    public function getName(): string;

    public function getPrice(): string;

    public function getPriceForUnit(string $unitName): string;

    public function getOriginalPrice(): string;

    public function getDiscount(): string;

    public function getTaxRate(): string;

    public function getTax(): string;

    public function getIsOutOfStock(): bool;

    public function getQuantityPriceRules(): array;

    public function getQuantityPriceRulesForUnit(ProductUnitInterface $unit): array;

    /**
     * @throws ElementNotFoundException
     */
    public function addToCart(): void;

    /**
     * @throws ElementNotFoundException
     */
    public function addToCartWithQuantity(string $quantity): void;

    /**
     * @throws ElementNotFoundException
     */
    public function addToCartInUnit(ProductUnitDefinitionInterface $unit): void;

    /**
     * @throws ElementNotFoundException
     */
    public function addToCartInUnitWithQuantity(ProductUnitDefinitionInterface $unit, string $quantity): void;
}
