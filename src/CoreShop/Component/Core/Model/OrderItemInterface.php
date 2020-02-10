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

use CoreShop\Component\Order\Model\OrderItemInterface as BaseOrderItemInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use CoreShop\Component\Shipping\Model\ShippableItemInterface;

interface OrderItemInterface extends BaseOrderItemInterface, ShippableItemInterface
{
    /**
     * @return bool
     */
    public function getDigitalProduct();

    /**
     * @param bool $digitalProduct
     */
    public function setDigitalProduct($digitalProduct);

    /**
     * @return null|ProductUnitDefinitionInterface
     */
    public function getUnitDefinition();

    /**
     * @param ProductUnitDefinitionInterface $productUnitDefinition
     */
    public function setUnitDefinition($productUnitDefinition);

    /**
     * @return bool
     */
    public function hasUnitDefinition();

    /**
     * @return int
     */
    public function getDefaultUnitQuantity();

    /**
     * @param int $defaultUnitQuantity
     */
    public function setDefaultUnitQuantity($defaultUnitQuantity);

    /**
     * @return float
     */
    public function getItemWeight();

    /**
     * @return float
     */
    public function getTotalWeight();
}
