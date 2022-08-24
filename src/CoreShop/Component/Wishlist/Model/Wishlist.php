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

use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;
use CoreShop\Component\Store\Model\StoreAwareTrait;
use Webmozart\Assert\Assert;

abstract class Wishlist extends AbstractPimcoreModel implements WishlistInterface
{
    use StoreAwareTrait;

    public function hasItems(): bool
    {
        return is_array($this->getItems()) && count($this->getItems()) > 0;
    }

    public function addItem($item): void
    {
        Assert::isInstanceOf($item, WishlistItemInterface::class);

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
