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

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Bundle\CoreBundle\Event\CustomerRegistrationEvent;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\UserInterface;
use CoreShop\Component\Core\Model\WishlistInterface;
use CoreShop\Component\Wishlist\Context\WishlistContextInterface;
use CoreShop\Component\Wishlist\Context\WishlistNotFoundException;
use CoreShop\Component\Wishlist\Manager\WishlistManagerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

final class WishlistBlamerListener
{
    public function __construct(
        private WishlistContextInterface $wishlistContext
    )
    {
    }

    public function onInteractiveLogin(InteractiveLoginEvent $interactiveLoginEvent): void
    {
        $user = $interactiveLoginEvent->getAuthenticationToken()->getUser();
        if (!$user instanceof UserInterface) {
            return;
        }

        $customer = $user->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            return;
        }

        $this->blame($customer);
    }

    public function onRegisterEvent(CustomerRegistrationEvent $event): void
    {
        $user = $event->getCustomer();

        $this->blame($user);
    }

    private function blame(CustomerInterface $user): void
    {
        $wishlist = $this->getWishlist();

        if (null === $wishlist) {
            return;
        }

        $wishlist->setCustomer($user);
    }

    private function getWishlist(): ?WishlistInterface
    {
        try {
            $wishlist = $this->wishlistContext->getWishlist();

            if ($wishlist instanceof WishlistInterface) {
                return $wishlist;
            }

            return null;
        } catch (WishlistNotFoundException) {
            return null;
        }
    }
}
