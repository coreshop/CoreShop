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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\OrderBundle\DTO;

class AddMultipleToCart implements AddMultipleToCartInterface
{
    private array $items;

    public function __construct(
        array $items,
    ) {
        $this->items = $items;
    }

    /**
     * @return AddToCartInterface[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param AddToCartInterface[] $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    public function addItem(AddToCartInterface $addToCart): void
    {
        $this->items[] = $addToCart;
    }
}
