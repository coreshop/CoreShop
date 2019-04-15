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

use CoreShop\Component\Order\Model\SaleItemInterface as BaseSaleItemInterface;
use CoreShop\Component\Product\Model\ProductUnitInterface;

interface SaleItemInterface extends BaseSaleItemInterface
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
     * @return int
     */
    public function getObjectId();

    /**
     * @param int $objectId
     */
    public function setObjectId($objectId);

    /**
     * @return int
     */
    public function getMainObjectId();

    /**
     * @param int $mainObjectId
     */
    public function setMainObjectId($mainObjectId);

    /**
     * @return null|string
     */
    public function getUnitIdentifier();

    /**
     * @param string $unitIdentifier
     */
    public function setUnitIdentifier($unitIdentifier);

    /**
     * @return null|ProductUnitInterface
     */
    public function getUnit();

    /**
     * @param int $unitId
     */
    public function setUnit($unitId);

    /**
     * @return bool
     */
    public function hasUnit();

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
     * @param float $itemWeight
     */
    public function setItemWeight($itemWeight);

    /**
     * @return float
     */
    public function getTotalWeight();

    /**
     * @param float $totalWeight
     */
    public function setTotalWeight($totalWeight);
}
