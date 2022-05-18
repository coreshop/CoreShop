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

namespace CoreShop\Component\Wishlist\Model;

use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Resource\Exception\ImplementedByPimcoreException;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;

abstract class WishlistItem extends AbstractPimcoreModel implements WishlistItemInterface
{
    public function getWishlist(): WishlistInterface
    {
        $parent = $this->getParent();

        do {
            if ($parent instanceof WishlistInterface) {
                return $parent;
            }

            $parent = $parent->getParent();
        } while ($parent !== null);

        throw new \Exception('Wishlist Item does not have a valid Wishlist');
    }

    public function getProduct(): ?PurchasableInterface
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setProduct(?PurchasableInterface $product)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }
}
