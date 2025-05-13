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

namespace CoreShop\Component\StorageList\Factory;

use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;

class StorageListItemFactory implements StorageListItemFactoryInterface
{
    public function __construct(
        private FactoryInterface $storageListItemFactory,
    ) {
    }

    public function createNew()
    {
        return $this->storageListItemFactory->createNew();
    }

    public function createWithStorageListProduct(ResourceInterface $product): StorageListItemInterface
    {
        /**
         * @var StorageListItemInterface $item
         */
        $item = $this->storageListItemFactory->createNew();

        if ($item instanceof AbstractPimcoreModel) {
            $item->setKey(uniqid());
            $item->setPublished(true);
        }

        $item->setProduct($product);

        return $item;
    }
}
