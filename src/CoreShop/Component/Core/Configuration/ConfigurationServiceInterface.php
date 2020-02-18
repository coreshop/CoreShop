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

namespace CoreShop\Component\Core\Configuration;

use CoreShop\Component\Configuration\Service\ConfigurationServiceInterface as BaseConfigurationServiceInterface;
use CoreShop\Component\Core\Model\ConfigurationInterface;
use CoreShop\Component\Store\Model\StoreInterface;

interface ConfigurationServiceInterface extends BaseConfigurationServiceInterface
{
    /**
     * @param string              $key
     * @param StoreInterface|null $store
     * @param bool                $returnObject
     *
     * @return ConfigurationInterface|mixed|null
     */
    public function getForStore($key, StoreInterface $store = null, $returnObject = false);

    /**
     * @param string              $key
     * @param mixed               $data
     * @param StoreInterface|null $store
     *
     * @return ConfigurationInterface
     */
    public function setForStore($key, $data, StoreInterface $store = null);

    /**
     * @param string              $key
     * @param StoreInterface|null $store
     *
     * @return ConfigurationInterface
     */
    public function removeForStore($key, StoreInterface $store = null);
}
