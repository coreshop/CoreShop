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

namespace CoreShop\Bundle\WishlistBundle\Factory;

use CoreShop\Bundle\WishlistBundle\DTO\AddToWishlistInterface;
use CoreShop\Component\Wishlist\Model\WishlistInterface;
use CoreShop\Component\Wishlist\Model\WishlistItemInterface;

class AddToWishlistFactory implements AddToWishlistFactoryInterface
{
    /**
     * @psalm-param class-string $addToWishlistClass
     */
    public function __construct(
        protected string $addToWishlistClass
    ) {
    }

    public function createWithWishlistAndWishlistItem(WishlistInterface $wishlist, WishlistItemInterface $wishlistItem): AddToWishlistInterface
    {
        $class = new $this->addToWishlistClass($wishlist, $wishlistItem);

        if (!in_array(AddToWishlistInterface::class, class_implements($class), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s".', $class::class, AddToWishlistInterface::class)
            );
        }

        return $class;
    }
}
