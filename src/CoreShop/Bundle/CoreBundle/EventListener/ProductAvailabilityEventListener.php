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

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Order\Repository\CartItemRepositoryInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\FactoryInterface;

final class ProductAvailabilityEventListener
{
    /**
     * @var CartItemRepositoryInterface
     */
    private $cartItemRepository;

    /**
     * @var FactoryInterface
     */
    private $pimcoreModelFactory;

    /**
     * @var int[]
     */
    private $productIdsToCheck = [];

    /**
     * @param CartItemRepositoryInterface $cartItemRepository
     * @param FactoryInterface            $pimcoreModelFactory
     */
    public function __construct(
        CartItemRepositoryInterface $cartItemRepository,
        FactoryInterface $pimcoreModelFactory
    ) {
        $this->cartItemRepository = $cartItemRepository;
        $this->pimcoreModelFactory = $pimcoreModelFactory;
    }

    /**
     * @param DataObjectEvent $event
     */
    public function preUpdateListener(DataObjectEvent $event)
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

    /**
     * @param DataObjectEvent $event
     */
    public function postUpdateListener(DataObjectEvent $event)
    {
        $object = $event->getObject();

        if (!$object instanceof PurchasableInterface) {
            return;
        }

        if (!in_array($object->getId(), $this->productIdsToCheck, true)) {
            return;
        }

        unset($this->productIdsToCheck[$object->getId()]);

        $cartItems = $this->cartItemRepository->findCartItemsByProductId($object->getId());

        if (count($cartItems) === 0) {
            return;
        }

        $this->informCarts($cartItems);
    }

    /**
     * @param DataObjectEvent $event
     */
    public function postDeleteListener(DataObjectEvent $event)
    {
        $object = $event->getObject();

        if (!$object instanceof PurchasableInterface) {
            return;
        }

        $cartItems = $this->cartItemRepository->findCartItemsByProductId($object->getId());

        if (count($cartItems) === 0) {
            return;
        }

        $this->informCarts($cartItems);
    }

    /**
     * @param array $cartItems
     */
    private function informCarts($cartItems)
    {
        /** @var CartItemInterface $cartItem */
        foreach ($cartItems as $cartItem) {
            $cart = $cartItem->getCart();
            if (!$cart instanceof CartInterface) {
                continue;
            }

            if ($cart->getOrder() instanceof OrderInterface) {
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
