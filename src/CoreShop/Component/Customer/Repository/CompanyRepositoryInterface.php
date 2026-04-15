<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\Customer\Repository;

use CoreShop\Component\Customer\Model\CompanyInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;

interface CompanyRepositoryInterface extends PimcoreRepositoryInterface
{
    public function findCompanyByName(string $name): ?CompanyInterface;
}
