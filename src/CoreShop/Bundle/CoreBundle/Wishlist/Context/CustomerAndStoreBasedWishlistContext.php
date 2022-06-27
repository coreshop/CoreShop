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

namespace CoreShop\Bundle\CoreBundle\Wishlist\Context;

use CoreShop\Component\Core\Wishlist\Repository\WishlistRepositoryInterface;
use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Customer\Context\CustomerNotFoundException;
use CoreShop\Component\Order\Context\CartNotFoundException;
use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;
use CoreShop\Component\Wishlist\Context\WishlistContextInterface;
use CoreShop\Component\Wishlist\Context\WishlistNotFoundException;
use CoreShop\Component\Wishlist\Model\WishlistInterface;

final class CustomerAndStoreBasedWishlistContext implements WishlistContextInterface
{
    public function __construct(
        private CustomerContextInterface $customerContext,
        private StoreContextInterface $storeContext,
        private WishlistRepositoryInterface $wishlistRepository
    ) {
    }

    public function getWishlist(): WishlistInterface
    {
        try {
            $store = $this->storeContext->getStore();
        } catch (StoreNotFoundException) {
            throw new WishlistNotFoundException('CoreShop was not able to find the cart, as there is no current store.');
        }

        try {
            $customer = $this->customerContext->getCustomer();
        } catch (CustomerNotFoundException) {
            throw new WishlistNotFoundException('CoreShop was not able to find the wishlist, as there is no logged in user.');
        }

        $wishlist = $this->wishlistRepository->findLatestByStoreAndCustomer($store, $customer);

        if (null === $wishlist) {
            throw new WishlistNotFoundException(
                'CoreShop was not able to find the wishlist for currently logged in user.'
            );
        }

        return $wishlist;
    }
}
