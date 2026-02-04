<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
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
