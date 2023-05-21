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

namespace CoreShop\Component\StorageList\Manager;

use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;
use CoreShop\Component\Resource\Service\FolderCreationServiceInterface;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\StorageList\StorageListManagerInterface;
use Doctrine\DBAL\Connection;

final class StorageListPimcoreModelManager implements StorageListManagerInterface
{
    public function __construct(
        private FolderCreationServiceInterface $folderCreationService,
        private Connection $connection,
    ) {
    }

    public function persist(StorageListInterface $storageList): void
    {
        if (!$storageList instanceof AbstractPimcoreModel) {
            throw new \Exception('StorageList implementation needs to be a Pimcore Model');
        }

        $folder = $this->folderCreationService->createFolderForResource($storageList, [
            'suffix' => date('Y/m/d'),
            'path' => 'storage-list',
        ]);

        VersionHelper::useVersioning(function () use ($storageList, $folder) {
            $tempItems = $storageList->getItems();

            if (!$storageList->getId()) {
                $storageList->setItems([]);

                /**
                 * @psalm-suppress DocblockTypeContradiction
                 */
                if (!$storageList->getParent()) {
                    $storageList->setParent($folder);
                }

                $storageList->save();
            }

            /**
             * @var AbstractPimcoreModel $item
             */
            foreach ($tempItems as $index => $item) {
                $item->setParent(
                    $this->folderCreationService->createFolderForResource(
                        $item,
                        ['prefix' => $storageList->getFullPath()],
                    ),
                );
                $item->setPublished(true);
                $item->setKey($index + 1);
                $item->save();
            }

            $storageList->setItems($tempItems);

            /**
             * @var AbstractPimcoreModel $storageListItem
             */
            foreach ($storageList->getItems() as $storageListItem) {
                $storageListItem->save();
            }

            $storageList->save();
        }, false);
    }
}
