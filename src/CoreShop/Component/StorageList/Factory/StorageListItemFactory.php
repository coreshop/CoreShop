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

declare(strict_types=1);

namespace CoreShop\Component\StorageList\Factory;

use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;

class StorageListItemFactory implements StorageListItemFactoryInterface
{
    public function __construct(private FactoryInterface $storageListItemFactory)
    {
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
