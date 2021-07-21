<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\OrderBundle\Manager;

use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use CoreShop\Component\Resource\Service\FolderCreationServiceInterface;

final class CartManager implements CartManagerInterface
{
    private CartProcessorInterface $cartProcessor;
    private FolderCreationServiceInterface $folderCreationService;

    public function __construct(
        CartProcessorInterface $cartProcessor,
        FolderCreationServiceInterface $folderCreationService
    ) {
        $this->cartProcessor = $cartProcessor;
        $this->folderCreationService = $folderCreationService;
    }

    public function persistCart(OrderInterface $cart): void
    {
        $cartsFolder = $this->folderCreationService->createFolderForResource($cart, [
            'suffix' => date('Y/m/d'),
            'path' => 'cart'
        ]);

        VersionHelper::useVersioning(function () use ($cart, $cartsFolder) {
            $tempItems = $cart->getItems();

            if (!$cart->getId()) {
                $cart->setItems([]);

                if (!$cart->getParent()) {
                    $cart->setParent($cartsFolder);
                }

                $cart->save();
            }

            /**
             * @var OrderItemInterface $item
             */
            foreach ($tempItems as $index => $item) {
                $tempUnits = $item->getUnits();

                $item->setUnits([]);
                $item->setParent(
                    $this->folderCreationService->createFolderForResource(
                        $item,
                        ['prefix' => $cart->getFullPath()]
                    )
                );
                $item->setPublished(true);
                $item->setKey($index+1);
                $item->save();

                $item->setUnits($tempUnits);

                foreach ($item->getUnits() ?? [] as $unitIndex => $unit) {
                    $unit->setParent($item);
                    $unit->setPublished(true);
                    $unit->setKey($unitIndex+1);
                }
            }

            $cart->setItems($tempItems);
            $this->cartProcessor->process($cart);

            /**
             * @var OrderItemInterface $cartItem
             */
            foreach ($cart->getItems() as $cartItem) {
                foreach ($cartItem->getUnits() as $unit) {
                    $unit->save();
                }

                $cartItem->save();
            }

            $cart->save();
        }, false);
    }
}
