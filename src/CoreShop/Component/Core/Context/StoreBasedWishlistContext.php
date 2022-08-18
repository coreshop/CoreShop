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
use CoreShop\Component\StorageList\Context\StorageListContextInterface;
use CoreShop\Component\StorageList\Context\StorageListNotFoundException;
use CoreShop\Component\Store\Context\StoreNotFoundException;
use CoreShop\Component\Wishlist\Model\WishlistInterface;

final class StoreBasedWishlistContext implements StorageListContextInterface
{
    private ?WishlistInterface $wishlist = null;

    public function __construct(
        private StorageListContextInterface $wishlistContext,
        private ShopperContextInterface $shopperContext
    )
    {
    }

    public function getStorageList(): WishlistInterface
    {
        if (null !== $this->wishlist) {
            return $this->wishlist;
        }

        /**
         * @var \CoreShop\Component\Core\Model\WishlistInterface $wishlist
         */
        $wishlist = $this->wishlistContext->getStorageList();

        try {
            /** @var StoreInterface $store */
            $store = $this->shopperContext->getStore();

            $wishlist->setStore($store);
        } catch (StoreNotFoundException|CurrencyNotFoundException|LocaleNotFoundException $exception) {
            throw new StorageListNotFoundException('CoreShop was not able to prepare the wishlist.', $exception);
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
