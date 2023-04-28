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

namespace CoreShop\Bundle\OrderBundle\Manager;

use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use CoreShop\Component\Resource\Service\FolderCreationServiceInterface;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\StorageList\StorageListManagerInterface;
use Webmozart\Assert\Assert;

final class CartManager implements CartManagerInterface, StorageListManagerInterface
{
    public function __construct(
        private CartProcessorInterface $cartProcessor,
        private FolderCreationServiceInterface $folderCreationService,
    ) {
    }

    public function persist(StorageListInterface $storageList): void
    {
        Assert::isInstanceOf($storageList, OrderInterface::class);

        $this->persistCart($storageList);
    }

    public function persistCart(OrderInterface $cart): void
    {
        $cartsFolder = $this->folderCreationService->createFolderForResource($cart, [
            'suffix' => date('Y/m/d'),
            'path' => 'cart',
        ]);

        VersionHelper::useVersioning(function () use ($cart, $cartsFolder) {
            if (!$cart->getId()) {
                $tempItems = $cart->getItems();
                $cart->setItems([]);

                /**
                 * @psalm-suppress DocblockTypeContradiction
                 */
                if (!$cart->getParent()) {
                    $cart->setParent($cartsFolder);
                }

                $cart->save();
                $cart->setItems($tempItems);
            }

            $items = array_values($cart->getObjectVar('items') ?? []);

            /**
             * @var OrderItemInterface $item
             */
            foreach ($items as $index => $item) {
                $item->setParent(
                    $this->folderCreationService->createFolderForResource(
                        $item,
                        ['prefix' => $cart->getFullPath()],
                    ),
                );
                //$item->setPath($cart->getFullPath());
                $item->setPublished(true);
                $item->setKey((string)((int)$index + 1));
                $item->save();
            }

            $this->cartProcessor->process($cart);

            /**
             * @var OrderItemInterface $cartItem
             */
            foreach ($cart->getItems() as $cartItem) {
                $cartItem->save();
            }

            $cart->save();
        }, false);
    }
}
