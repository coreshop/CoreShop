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

namespace CoreShop\Component\StorageList\Context;

use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;
use CoreShop\Component\StorageList\Model\StorageListInterface;

final class StorageListFactoryContext implements StorageListContextInterface
{
    public function __construct(
        private FactoryInterface $storageListFactory,
    ) {
    }

    public function getStorageList(): StorageListInterface
    {
        /**
         * @var StorageListInterface $storageList
         */
        $storageList = $this->storageListFactory->createNew();

        if ($storageList instanceof AbstractPimcoreModel) {
            $storageList->setKey(uniqid());
            $storageList->setPublished(true);
        }

        return $storageList;
    }
}
