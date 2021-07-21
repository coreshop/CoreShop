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
