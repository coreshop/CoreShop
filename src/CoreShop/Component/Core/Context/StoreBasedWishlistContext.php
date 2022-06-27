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

namespace CoreShop\Component\Core\Context;

use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Currency\Context\CurrencyNotFoundException;
use CoreShop\Component\Locale\Context\LocaleNotFoundException;
use CoreShop\Component\Store\Context\StoreNotFoundException;
use CoreShop\Component\Wishlist\Context\WishlistContextInterface;
use CoreShop\Component\Wishlist\Context\WishlistNotFoundException;
use CoreShop\Component\Wishlist\Model\WishlistInterface;

final class StoreBasedWishlistContext implements WishlistContextInterface
{
    private ?WishlistInterface $wishlist = null;

    public function __construct(
        private WishlistContextInterface $wishlistContext,
        private ShopperContextInterface $shopperContext
    )
    {
    }

    public function getWishlist(): WishlistInterface
    {
        if (null !== $this->wishlist) {
            return $this->wishlist;
        }

        $wishlist = $this->wishlistContext->getwishlist();

        try {
            /** @var StoreInterface $store */
            $store = $this->shopperContext->getStore();

            $wishlist->setStore($store);
        } catch (StoreNotFoundException $exception) {
            throw new WishlistNotFoundException('CoreShop was not able to prepare the wishlist.', $exception);
        } catch (CurrencyNotFoundException $exception) {
            throw new WishlistNotFoundException('CoreShop was not able to prepare the wishlist.', $exception);
        } catch (LocaleNotFoundException $exception) {
            throw new WishlistNotFoundException('CoreShop was not able to prepare the wishlist.', $exception);
        }

        if ($this->shopperContext->hasCustomer()) {
            /**
             * @var CustomerInterface $customer
             */
            $customer = $this->shopperContext->getCustomer();
            $wishlist->setCustomer($customer);
        }

        $this->wishlist = $wishlist;

        return $wishlist;
    }
}
