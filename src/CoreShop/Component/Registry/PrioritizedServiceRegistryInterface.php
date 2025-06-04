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

namespace CoreShop\Component\Registry;

interface PrioritizedServiceRegistryInterface
{
    public function all(): array;

    public function register(string $identifier, int $priority, object $service): void;

    public function unregister(string $identifier): void;

    public function has(string $identifier): bool;

    public function get(string $identifier): object;

    public function getPreviousTo(string $identifier): ?object;

    public function hasPreviousTo(string $identifier): bool;

    public function getAllPreviousTo(string $identifier): array;

    public function getNextTo(string $identifier): ?object;

    public function hasNextTo(string $identifier): bool;

    public function getIndex(string $identifier): int;
}
