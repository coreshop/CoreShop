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

namespace CoreShop\Bundle\FixtureBundle\Repository;

use CoreShop\Bundle\FixtureBundle\Model\DataFixture;
use CoreShop\Component\Resource\Repository\RepositoryInterface;

interface DataFixtureRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $className
     *
     * @return DataFixture[]
     */
    public function findByClassName($className);

    /**
     * @param string $where
     * @param array  $parameters
     *
     * @return bool
     */
    public function isDataFixtureExists($where, array $parameters = []);

    /**
     * Update data fixture history.
     *
     * @param array  $updateFields assoc array with field names and values that should be updated
     * @param string $where        condition
     * @param array  $parameters   optional parameters for where condition
     */
    public function updateDataFixtureHistory(array $updateFields, $where, array $parameters = []);
}
