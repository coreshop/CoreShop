<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Order\Model\CartItem as BaseCartItem;

class CartItem extends BaseCartItem implements CartItemInterface
{
    use ProposalItemTrait;

    /**
     * {@inheritdoc}
     */
    public function getTotalWeight()
    {
        return $this->getItemWeight() * $this->getQuantity();
    }

    /**
     * {@inheritdoc}
     */
    public function getItemWeight()
    {
        return $this->getWeight();
    }

    /**
     * {@inheritdoc}
     */
    public function getWidth()
    {
        return $this->getProduct() instanceof ProductInterface ? $this->getProduct()->getWidth() : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeight()
    {
        return $this->getProduct() instanceof ProductInterface ? $this->getProduct()->getHeight() : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getDepth()
    {
        return $this->getProduct() instanceof ProductInterface ? $this->getProduct()->getDepth() : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getWeight()
    {
        return $this->getProduct() instanceof ProductInterface ? $this->getProduct()->getWeight() : 0;
    }
}
