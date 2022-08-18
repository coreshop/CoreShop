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
use CoreShop\Component\StorageList\Context\StorageListContextInterface;
use CoreShop\Component\StorageList\Context\StorageListNotFoundException;
use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;
use CoreShop\Component\Wishlist\Model\WishlistInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class SessionAndStoreBasedWishlistContext implements StorageListContextInterface
{
    private ?WishlistInterface $wishlist = null;

    public function __construct(
        private SessionInterface $session,
        private string $sessionKeyName,
        private WishlistRepositoryInterface $wishlistRepository,
        private StoreContextInterface $storeContext
    ) {
    }

    public function getStorageList(): WishlistInterface
    {
        if (null !== $this->wishlist) {
            return $this->wishlist;
        }

        try {
            $store = $this->storeContext->getStore();
        } catch (StoreNotFoundException $exception) {
            throw new StorageListNotFoundException($exception->getMessage(), $exception);
        }

        if (!$this->session->has(sprintf('%s.%s', $this->sessionKeyName, $store->getId()))) {
            throw new StorageListNotFoundException('CoreShop was not able to find the wishlist in session');
        }

        $wishlistId = $this->session->get(sprintf('%s.%s', $this->sessionKeyName, $store->getId()));

        if (!is_int($wishlistId)) {
            throw new StorageListNotFoundException('CoreShop was not able to find the wishlist in session');
        }

        /**
         * @var \CoreShop\Component\Core\Model\WishlistInterface|null $wishlist
         */
        $wishlist = $this->wishlistRepository->find($wishlistId);

        if (null === $wishlist || null === $wishlist->getStore() || $wishlist->getStore()->getId() !== $store->getId()) {
            $wishlist = null;
        }

        if (null === $wishlist) {
            $this->session->remove(sprintf('%s.%s', $this->sessionKeyName, $store->getId()));

            throw new StorageListNotFoundException('CoreShop was not able to find the wishlist in session');
        }

        $this->wishlist = $wishlist;

        return $wishlist;
    }
}
