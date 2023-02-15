<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Wishlist\Model;

use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;
use CoreShop\Component\Store\Model\StoreAwareTrait;
use Webmozart\Assert\Assert;

abstract class Wishlist extends AbstractPimcoreModel implements WishlistInterface
{
    use StoreAwareTrait;

    public function listCanBeShared(): bool
    {
        return null !== $this->getId() && null !== $this->getToken();
    }

    public function hasItems(): bool
    {
        return is_array($this->getItems()) && count($this->getItems()) > 0;
    }

    public function addItem($item): void
    {
        /**
         * @var WishlistItemInterface $item
         */
        Assert::isInstanceOf($item, WishlistItemInterface::class);

        $item->setWishlist($this);

        $items = $this->getItems();
        $items[] = $item;

        $this->setItems($items);
    }

    public function removeItem($item): void
    {
        $items = $this->getItems();

        foreach ($items as $i => $iValue) {
            $arrayItem = $iValue;

            if ($arrayItem->getId() === $item->getId()) {
                unset($items[$i]);

                break;
            }
        }

        $this->setItems(array_values($items));
    }

    public function hasItem($item): bool
    {
        $items = $this->getItems();

        foreach ($items as $iValue) {
            $arrayItem = $iValue;

            if ($arrayItem->getId() === $item->getId()) {
                return true;
            }
        }

        return false;
    }
}
