<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Order\Model\OrderItem as BaseOrderItem;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use CoreShop\Component\Resource\Exception\ImplementedByPimcoreException;

class OrderItem extends BaseOrderItem implements OrderItemInterface
{
    use ProposalItemTrait;
    use SaleItemTrait;

    /**
     * {@inheritdoc}
     */
    public function getUnitDefinition()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setUnitDefinition($productUnitDefinition)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function hasUnitDefinition()
    {
        return $this->getUnitDefinition() instanceof ProductUnitDefinitionInterface;
    }
}
