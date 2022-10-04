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

namespace CoreShop\Component\StorageList\Factory;

use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;

class StorageListFactory implements FactoryInterface
{
    public function __construct(
        private FactoryInterface $storageListFactory,
    ) {
    }

    public function createNew()
    {
        $storageList = $this->storageListFactory->createNew();

        if ($storageList instanceof AbstractPimcoreModel) {
            $storageList->setKey(uniqid('wishlist', true));
            $storageList->setPublished(true);
        }

        return $storageList;
    }
}
