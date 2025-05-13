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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Behat\Element\Pimcore;

interface MenuElementInterface
{
    public function hasMenuWithIdentifier(string $id): bool;

    public function openMenuWithIdentifier(string $id): void;

    public function aMenuIsOpen(): bool;

    public function openMenuHasItems(int $count): bool;

    public function hoverOverItemWithName(string $name): void;

    public function twoMenusShouldBeOpen(): bool;
}
