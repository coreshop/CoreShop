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

namespace CoreShop\Bundle\OrderBundle\Manager;

use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use CoreShop\Component\Pimcore\DataObject\ObjectServiceInterface;

final class CartManager implements CartManagerInterface
{
    /**
     * @var ObjectServiceInterface
     */
    private $objectService;

    /**
     * @var string
     */
    private $cartFolderPath;

    /**
     * @var CartProcessorInterface
     */
    private $cartProcessor;

    /**
     * @param CartProcessorInterface $cartProcessor
     * @param ObjectServiceInterface $objectService
     * @param string                 $cartFolderPath
     */
    public function __construct(
        CartProcessorInterface $cartProcessor,
        ObjectServiceInterface $objectService,
        $cartFolderPath
    ) {
        $this->cartProcessor = $cartProcessor;
        $this->objectService = $objectService;
        $this->cartFolderPath = $cartFolderPath;
    }

    /**
     * {@inheritdoc}
     */
    public function persistCart(OrderInterface $cart): void
    {
        $cartsFolder = $this->objectService->createFolderByPath(sprintf('%s/%s', $this->cartFolderPath, date('Y/m/d')));

        VersionHelper::useVersioning(function () use ($cart, $cartsFolder) {
            $tempItems = $cart->getItems();

            if (!$cart->getId()) {
                $cart->setItems([]);
                $cart->setParent($cartsFolder);
                $cart->save();
            }

            /**
             * @var OrderItemInterface $item
             */
            foreach ($tempItems as $index => $item) {
                $item->setParent($cart);
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
