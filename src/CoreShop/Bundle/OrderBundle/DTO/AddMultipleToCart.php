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

namespace CoreShop\Bundle\OrderBundle\DTO;

class AddMultipleToCart implements AddMultipleToCartInterface
{
    /**
     * @var AddToCartInterface[]
     */
    private $items;

    /**
     * @param AddToCartInterface[] $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return AddToCartInterface[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param AddToCartInterface[] $items
     */
    public function setItems(array $items)
    {
        $this->items = $items;
    }

    /**
     * @param AddToCartInterface $addToCart
     */
    public function addItem(AddToCartInterface $addToCart)
    {
        $this->items[] = $addToCart;
    }
}
