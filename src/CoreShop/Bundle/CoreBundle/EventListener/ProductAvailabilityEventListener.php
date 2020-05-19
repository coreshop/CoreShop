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

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Order\OrderSaleStates;
use CoreShop\Component\Order\Repository\CartItemRepositoryInterface;
use CoreShop\Component\Order\Repository\OrderItemRepositoryInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\FactoryInterface;

final class ProductAvailabilityEventListener
{
    private $cartItemRepository;
    private $pimcoreModelFactory;
    private $productIdsToCheck = [];

    public function __construct(
        OrderItemRepositoryInterface $cartItemRepository,
        FactoryInterface $pimcoreModelFactory
    ) {
        $this->cartItemRepository = $cartItemRepository;
        $this->pimcoreModelFactory = $pimcoreModelFactory;
    }

    public function preUpdateListener(DataObjectEvent $event): void
    {
        $object = $event->getObject();

        if (!$object instanceof PurchasableInterface) {
            return;
        }

        if (in_array($object->getId(), $this->productIdsToCheck, true)) {
            return;
        }

        $originalItem = $this->pimcoreModelFactory->build(get_class($object));
        $originalItem->getDao()->getById($object->getId());

        if (!$originalItem instanceof PurchasableInterface) {
            return;
        }

        if (!$object instanceof Concrete) {
            return;
        }

        if (!$originalItem instanceof Concrete) {
            return;
        }

        if ($object->getPublished() === $originalItem->isPublished()) {
            return;
        }

        $this->productIdsToCheck[$object->getId()] = $object->getId();
    }

    public function postUpdateListener(DataObjectEvent $event): void
    {
        $object = $event->getObject();

        if (!$object instanceof PurchasableInterface) {
            return;
        }

        if (!in_array($object->getId(), $this->productIdsToCheck, true)) {
            return;
        }

        unset($this->productIdsToCheck[$object->getId()]);

        $cartItems = $this->cartItemRepository->findOrderItemsByProductId($object->getId());

        if (count($cartItems) === 0) {
            return;
        }

        $this->informCarts($cartItems);
    }

    public function postDeleteListener(DataObjectEvent $event): void
    {
        $object = $event->getObject();

        if (!$object instanceof PurchasableInterface) {
            return;
        }

        $cartItems = $this->cartItemRepository->findOrderItemsByProductId($object->getId());

        if (count($cartItems) === 0) {
            return;
        }

        $this->informCarts($cartItems);
    }

    private function informCarts(array $cartItems): void
    {
        /** @var OrderItemInterface $cartItem */
        foreach ($cartItems as $cartItem) {
            $cart = $cartItem->getOrder();
            if (!$cart instanceof OrderInterface) {
                continue;
            }

            if ($cart->getSaleState() !== OrderSaleStates::STATE_CART) {
                continue;
            }

            $cart->removeItem($cartItem);
            $cartItem->delete();

            VersionHelper::useVersioning(
                function () use ($cart) {
                    $cart->setNeedsRecalculation(true);
                    $cart->save();
                },
                false
            );
        }
    }
}
