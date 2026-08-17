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

namespace CoreShop\Component\Core\Repository;

use CoreShop\Component\Address\Repository\CountryRepositoryInterface as BaseCountryRepositoryInterface;
use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

interface CountryRepositoryInterface extends BaseCountryRepositoryInterface
{
    /**
     * @return CountryInterface[]
     */
    public function findForStore(StoreInterface $store): array;
}
