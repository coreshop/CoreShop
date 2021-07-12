<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\OrderBundle\Manager;

use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Pimcore\DataObject\ObjectServiceInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;

final class CartManager implements CartManagerInterface
{
    private CartProcessorInterface $cartProcessor;
    private ObjectServiceInterface $objectService;
    private string $cartFolderPath;
    private string $cartItemFolderPath;

    public function __construct(
        CartProcessorInterface $cartProcessor,
        ObjectServiceInterface $objectService,
        string $cartFolderPath,
        string $cartItemFolderPath
    ) {
        $this->cartProcessor = $cartProcessor;
        $this->objectService = $objectService;
        $this->cartFolderPath = $cartFolderPath;
        $this->cartItemFolderPath = $cartItemFolderPath;
    }

    public function persistCart(OrderInterface $cart): void
    {
        $cartsFolder = $this->objectService->createFolderByPath(sprintf('%s/%s', $this->cartFolderPath, date('Y/m/d')));

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
                $item->setParent(
                    $this->objectService->createFolderByPath(
                        sprintf('%s/%s', $cart->getFullPath(), $this->cartItemFolderPath)
                    )
                );
                $item->setPublished(true);
                $item->setKey($index+1);
                $item->save();
            }

            $cart->setItems($tempItems);
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
