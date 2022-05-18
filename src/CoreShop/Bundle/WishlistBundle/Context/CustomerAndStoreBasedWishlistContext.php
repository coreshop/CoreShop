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

namespace CoreShop\Bundle\WishlistBundle\Context;

use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Customer\Context\CustomerNotFoundException;
use CoreShop\Component\Wishlist\Context\WishlistContextInterface;
use CoreShop\Component\Wishlist\Context\WishlistNotFoundException;
use CoreShop\Component\Wishlist\Model\WishlistInterface;
use CoreShop\Component\Wishlist\Repository\WishlistRepositoryInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;
use Pimcore\Http\RequestHelper;

final class CustomerAndStoreBasedWishlistContext implements WishlistContextInterface
{
    public function __construct(private CustomerContextInterface $customerContext, private StoreContextInterface $storeContext, private WishlistRepositoryInterface $wishlistRepository, private RequestHelper $pimcoreRequestHelper)
    {
    }

    public function getWishlist(): WishlistInterface
    {
        /**
         * @psalm-suppress DeprecatedMethod
         */
        if (
            $this->pimcoreRequestHelper->hasMasterRequest() &&
            $this->pimcoreRequestHelper->getMasterRequest()->attributes->get('_route') !== 'coreshop_login_check'
        ) {
            throw new WishlistNotFoundException('CustomerAndStoreBasedWishlistContext can only be applied in coreshop_login_check route.');
        }

        try {
            $store = $this->storeContext->getStore();
        } catch (StoreNotFoundException) {
            throw new WishlistNotFoundException('CoreShop was not able to find the wishlist, as there is no current store.');
        }

        try {
            $customer = $this->customerContext->getCustomer();
        } catch (CustomerNotFoundException) {
            throw new WishlistNotFoundException('CoreShop was not able to find the wishlist, as there is no logged in user.');
        }

        $wishlist = $this->wishlistRepository->findLatestWishlistByStoreAndCustomer($store, $customer);
        if (null === $wishlist) {
            throw new WishlistNotFoundException('CoreShop was not able to find the wishlist for currently logged in user.');
        }

        return $wishlist;
    }
}
