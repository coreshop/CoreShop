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

namespace CoreShop\Bundle\OrderBundle\Manager;

use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use CoreShop\Component\Resource\Service\FolderCreationServiceInterface;
use Doctrine\DBAL\Connection;

final class CartManager implements CartManagerInterface
{
    public function __construct(private CartProcessorInterface $cartProcessor, private FolderCreationServiceInterface $folderCreationService, private Connection $connection)
    {
    }

    public function persistCart(OrderInterface $cart): void
    {
        $cartsFolder = $this->folderCreationService->createFolderForResource($cart, [
            'suffix' => date('Y/m/d'),
            'path' => 'cart'
        ]);

        $this->connection->transactional(function()  use ($cart, $cartsFolder) {
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
                        $this->folderCreationService->createFolderForResource(
                            $item,
                            ['prefix' => $cart->getFullPath()]
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
        });
    }
}
