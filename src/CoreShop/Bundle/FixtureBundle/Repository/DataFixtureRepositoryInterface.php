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
