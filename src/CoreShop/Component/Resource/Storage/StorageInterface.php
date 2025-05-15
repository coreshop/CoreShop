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

namespace CoreShop\Component\Resource\Storage;

interface StorageInterface
{
    public function has(string $name): bool;

    public function get(string $name, mixed $default = null): mixed;

    public function set(string $name, mixed $value): void;

    public function remove(string $name): void;

    public function all(): array;
}
