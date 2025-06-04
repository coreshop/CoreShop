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

namespace CoreShop\Component\Store\Model;

use Doctrine\Common\Collections\Collection;

interface StoresAwareInterface
{
    /**
     * @return Collection|StoreInterface[]
     */
    public function getStores();

    /**
     * @return bool
     */
    public function hasStore(StoreInterface $store);

    public function addStore(StoreInterface $store);

    public function removeStore(StoreInterface $store);
}
