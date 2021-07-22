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

namespace CoreShop\Behat\Service;

interface SharedStorageInterface
{
    public function get(string $key): mixed;

    public function has(string $key): bool;

    public function set(string $key, mixed $resource): void;

    public function getLatestResource(): mixed;

    public function setClipboard(array $clipboard): void;
}
