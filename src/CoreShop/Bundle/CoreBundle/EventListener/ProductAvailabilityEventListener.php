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

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Order\Repository\CartItemRepositoryInterface;
use CoreShop\Component\Order\Repository\CartRepositoryInterface;
use CoreShop\Component\Pimcore\VersionHelper;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Model\Version;

final class ProductAvailabilityEventListener
{
    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var CartItemRepositoryInterface
     */
    private $cartItemRepository;

    /**
     * @param CartRepositoryInterface     $cartRepository
     * @param CartItemRepositoryInterface $cartItemRepository
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        CartItemRepositoryInterface $cartItemRepository
    ) {
        $this->cartRepository = $cartRepository;
        $this->cartItemRepository = $cartItemRepository;
    }

    /**
     * @param DataObjectEvent $event
     */
    public function checkCartsAfterUpdate(DataObjectEvent $event)
    {
        $object = $event->getObject();

        if (!$object instanceof PurchasableInterface) {
            return;
        }

        // we need to check if product was active before! if no version is available, we can't do anything.
        $versions = $object->getVersions();
        if (empty($versions) || !$versions[1] instanceof Version) {
            return;
        }

        /** @var \Pimcore\Model\Version $currentVersion */
        $currentVersion = $versions[0];
        if ($currentVersion->getData()->isPublished() === true) {
            return;
        }

        /** @var \Pimcore\Model\Version $prevVersion */
        $prevVersion = $versions[1];
        if ($prevVersion->getData()->isPublished() === false) {
            return;
        }

        $cartItems = $this->cartItemRepository->findCartItemsByProductId($object->getId());

        if (count($cartItems) === 0) {
            return;
        }

        $this->informCarts($cartItems);

    }

    /**
     * @param DataObjectEvent $event
     */
    public function checkCartsAfterDelete(DataObjectEvent $event)
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
     * @param $cartItems
     */
    private function informCarts($cartItems)
    {
        /** @var CartItemInterface $cartItem */
        foreach ($cartItems as $cartItem) {
            $cart = $cartItem->getParent();
            if (!$cart instanceof CartInterface) {
                continue;
            }

            if ($cart->getOrder() instanceof OrderInterface) {
                continue;
            }

            $cart->removeItem($cartItem);

            VersionHelper::useVersioning(function () use ($cart) {
                $cart->setNeedsRecalculation(true);
                $cart->save();
            }, false);
        }
    }
}