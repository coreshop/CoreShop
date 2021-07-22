<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Customer\Repository;

use CoreShop\Component\Customer\Model\CompanyInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;

interface CompanyRepositoryInterface extends PimcoreRepositoryInterface
{
    /**
     * Find Company by Name.
     *
     * @param string $name
     *
     * @return CompanyInterface|null
     */
    public function findCompanyByName($name);
}
