<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Configuration\Repository;

use CoreShop\Component\Configuration\Model\ConfigurationInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;

interface ConfigurationRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $key
     *
     * @return ConfigurationInterface
     */
    public function findByKey($key);
}
