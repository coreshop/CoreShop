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

namespace CoreShop\Component\StorageList\Model;

interface StorageListInterface extends \Countable
{
    public function getId(): ?int;

    public function getItems(): ?array;

    public function setItems(?array $items);

    public function hasItems(): bool;

    public function addItem($item): void;

    public function removeItem($item): void;

    public function hasItem($item): bool;
}
