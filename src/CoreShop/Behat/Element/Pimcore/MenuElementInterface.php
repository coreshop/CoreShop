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

namespace CoreShop\Behat\Element\Pimcore;

use Behat\Mink\Exception\ElementNotFoundException;

interface MenuElementInterface
{
    public function hasMenuWithIdentifier(string $id): bool;

    public function openMenuWithIdentifier(string $id): void;

    public function aMenuIsOpen(): bool;

    public function openMenuHasItems(int $count): bool;

    public function hoverOverItemWithName(string $name): void;

    public function twoMenusShouldBeOpen(): bool;
}
