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
        /**
         * @var OrderInterface $storageList
         */
        Assert::isInstanceOf($storageList, OrderInterface::class);

        $this->persistCart($storageList);
    }

    public function persistCart(OrderInterface $cart/*, array $params = []*/): void
    {
        $cartsFolder = $this->folderCreationService->createFolderForResource($cart, [
            'suffix' => date('Y/m/d'),
            'path' => 'cart',
        ]);

        $params = [];
        if (func_num_args() === 2) {
            $params = func_get_arg(1) ?? [];
        }

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
                $item->setKey(uniqid(sprintf('%s.', $index + 1), true));
                $item->setPublished(true);
                $item->save();
            }

            /**
             * The CartProcessor might add new Items to the Cart (eg. Gift Products)
             * so we need to set the Parent and Key after the CartProcessor has been processed
             */
            $this->cartProcessor->process($cart);

            /**
             * @var OrderItemInterface $item
             */
            foreach ($cart->getItems() as $index => $item) {
                $item->setParent(
                    $this->folderCreationService->createFolderForResource(
                        $item,
                        ['prefix' => $cart->getFullPath()],
                    ),
                );
                $item->setKey(uniqid(sprintf('%s.', ((int) $index + 1)), true));
                $item->save();
            }

            $cart->save();
        }, $params['enable_versioning'] ?? false);
    }
}
