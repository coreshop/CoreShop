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

use CoreShop\Component\Customer\Model\CustomerAwareInterface;
use CoreShop\Component\Locale\Model\LocaleAwareInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\Store\Model\StoreAwareInterface;

interface WishlistInterface extends
    PimcoreModelInterface,
    StoreAwareInterface,
    LocaleAwareInterface,
    CustomerAwareInterface,
    StorageListInterface
{
    public function getToken(): ?string;

    public function setToken(?string $token);

    public function getName(): ?string;

    public function setName(?string $token);

    /**
     * @return WishlistItemInterface[]|null
     */
    public function getItems(): ?array;

    /**
     * @param WishlistItemInterface[] $items
     */
    public function setItems(?array $items);

    public function hasItems(): bool;

    /**
     * @param WishlistItemInterface $item
     */
    public function addItem($item): void;

    /**
     * @param WishlistItemInterface $item
     */
    public function removeItem($item): void;

    /**
     * @param WishlistItemInterface $item
     */
    public function hasItem($item): bool;
}
