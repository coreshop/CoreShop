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

namespace CoreShop\Bundle\WishlistBundle\Manager;

use CoreShop\Component\Wishlist\Manager\WishlistManagerInterface;
use CoreShop\Component\Wishlist\Model\WishlistInterface;
use CoreShop\Component\Wishlist\Model\WishlistItemInterface;
use CoreShop\Component\Wishlist\Processor\WishlistProcessorInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use CoreShop\Component\Resource\Service\FolderCreationServiceInterface;
use Doctrine\DBAL\Connection;

final class WishlistManager implements WishlistManagerInterface
{
    public function __construct(private WishlistProcessorInterface $wishlistProcessor, private FolderCreationServiceInterface $folderCreationService, private Connection $connection)
    {
    }

    public function persistWishlist(WishlistInterface $wishlist): void
    {
        $wishlistsFolder = $this->folderCreationService->createFolderForResource($wishlist, [
            'suffix' => date('Y/m/d'),
            'path' => 'wishlist',
        ]);

        $this->connection->transactional(function () use ($wishlist, $wishlistsFolder) {
            VersionHelper::useVersioning(function () use ($wishlist, $wishlistsFolder) {
                $tempItems = $wishlist->getItems();

                if (!$wishlist->getId()) {
                    $wishlist->setItems([]);

                    /**
                     * @psalm-suppress DocblockTypeContradiction
                     */
                    if (!$wishlist->getParent()) {
                        $wishlist->setParent($wishlistsFolder);
                    }

                    $wishlist->save();
                }

                /**
                 * @var WishlistItemInterface $item
                 */
                foreach ($tempItems as $index => $item) {
                    $item->setParent(
                        $this->folderCreationService->createFolderForResource(
                            $item,
                            ['prefix' => $wishlist->getFullPath()]
                        )
                    );
                    $item->setPublished(true);
                    $item->setKey($index + 1);
                    $item->save();
                }

                $wishlist->setItems($tempItems);
                $this->wishlistProcessor->process($wishlist);

                /**
                 * @var WishlistItemInterface $wishlistItem
                 */
                foreach ($wishlist->getItems() as $wishlistItem) {
                    $wishlistItem->save();
                }

                $wishlist->save();
            }, false);
        });
    }
}
