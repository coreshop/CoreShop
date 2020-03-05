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

namespace CoreShop\Component\Resource\Storage;

interface StorageInterface
{
    /**
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get(string $name, $default = null);

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function set(string $name, $value): void;

    /**
     * @param string $name
     */
    public function remove(string $name): void;

    /**
     * @return array
     */
    public function all(): array;
}
