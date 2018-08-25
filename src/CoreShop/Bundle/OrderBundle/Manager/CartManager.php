<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\OrderBundle\Manager;

use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use CoreShop\Component\Pimcore\DataObject\ObjectServiceInterface;
use Doctrine\Common\Persistence\ObjectManager;

final class CartManager implements CartManagerInterface
{
    /**
     * @var ObjectServiceInterface
     */
    protected $objectService;

    /**
     * @var string
     */
    protected $cartFolderPath;

    /**
     * @var CartProcessorInterface
     */
    protected $cartProcessor;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @param CartProcessorInterface $cartProcessor
     * @param ObjectServiceInterface $objectService
     * @param ObjectManager $objectManager
     * @param string $cartFolderPath
     */
    public function __construct(
        CartProcessorInterface $cartProcessor,
        ObjectServiceInterface $objectService,
        ObjectManager $objectManager,
        $cartFolderPath
    )
    {
        $this->cartProcessor = $cartProcessor;
        $this->objectService = $objectService;
        $this->objectManager = $objectManager;
        $this->cartFolderPath = $cartFolderPath;
    }

    /**
     * {@inheritdoc}
     */
    public function persistCart(CartInterface $cart)
    {
        $cartsFolder = $this->objectService->createFolderByPath(sprintf('%s/%s', $this->cartFolderPath, date('Y/m/d')));

        VersionHelper::useVersioning(function() use ($cart, $cartsFolder) {
            $tempItems = $cart->getItems();

            if (!$cart->getId()) {
                $cart->setItems([]);
                $cart->setParent($cartsFolder);

                $this->objectManager->persist($cart);
                $this->objectManager->flush();
            }

            foreach ($tempItems as $index => $item) {
                $item->setParent($cart);

                $this->objectManager->persist($item);
                $this->objectManager->flush();
            }

            $cart->setItems($tempItems);
            $this->cartProcessor->process($cart);

            foreach ($cart->getItems() as $cartItem) {
                $this->objectManager->persist($cartItem);
                $this->objectManager->flush();
            }

            $this->objectManager->persist($cart);
            $this->objectManager->flush();
        }, false);
    }
}
