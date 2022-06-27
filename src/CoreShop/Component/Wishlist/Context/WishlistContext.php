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

namespace CoreShop\Component\Wishlist\Context;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\OrderInvoiceStates;
use CoreShop\Component\Order\OrderPaymentStates;
use CoreShop\Component\Order\OrderSaleStates;
use CoreShop\Component\Order\OrderShipmentStates;
use CoreShop\Component\Order\OrderStates;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Wishlist\Model\WishlistInterface;

final class WishlistContext implements WishlistContextInterface
{
    public function __construct(private FactoryInterface $wishlistFactory)
    {
    }

    public function getWishlist(): WishlistInterface
    {
        /**
         * @var WishlistInterface $wishlist
         */
        $wishlist = $this->wishlistFactory->createNew();
        $wishlist->setKey(uniqid());
        $wishlist->setPublished(true);

        return $wishlist;
    }
}
