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

namespace CoreShop\Component\Configuration\Service;

use CoreShop\Component\Configuration\Model\ConfigurationInterface;

interface ConfigurationServiceInterface
{
    /**
     * @param string $key
     * @param bool   $returnObject
     *
     * @return ConfigurationInterface|null
     */
    public function get(string $key, bool $returnObject = false);

    /**
     * @param string $key
     * @param mixed  $data
     *
     * @return ConfigurationInterface
     */
    public function set(string $key, $data): ConfigurationInterface;

    /**
     * @param string $key
     */
    public function remove(string $key): void;
}
