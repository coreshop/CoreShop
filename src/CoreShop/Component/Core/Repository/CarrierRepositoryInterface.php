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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Core\Repository;

use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

interface CarrierRepositoryInterface extends RepositoryInterface
{
    /**
     * @return CarrierInterface[]
     */
    public function findForStore(StoreInterface $store): array;

    /**
     * @return CarrierInterface[]
     */
    public function findForStoreIgnoreHideForCheckout(StoreInterface $store): array;
}
