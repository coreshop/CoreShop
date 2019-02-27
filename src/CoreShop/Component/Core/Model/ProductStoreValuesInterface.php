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

use CoreShop\Component\Product\Model\ProductAdditionalUnitInterface;
use CoreShop\Component\Product\Model\ProductUnitInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Store\Model\StoreAwareInterface;
use Doctrine\Common\Collections\Collection;

interface ProductStoreValuesInterface extends ResourceInterface, StoreAwareInterface
{
    /**
     * @return int
     */
    public function getPrice();

    /**
     * @param int $price
     */
    public function setPrice(int $price);

    /**
     * @return ProductUnitInterface
     */
    public function getDefaultUnit();

    /**
     * @param ProductUnitInterface $unit
     */
    public function setDefaultUnit(ProductUnitInterface $unit);

    /**
     * @return int
     */
    public function getDefaultUnitPrecision();

    /**
     * @param int $defaultUnitPrecision
     */
    public function setDefaultUnitPrecision(int $defaultUnitPrecision);

    /**
     * @param ProductAdditionalUnitInterface $productAdditionalUnit
     */
    public function addAdditionalUnit(ProductAdditionalUnitInterface $productAdditionalUnit);

    /**
     * @param ProductAdditionalUnitInterface $productAdditionalUnit
     */
    public function removeAdditionalUnit(ProductAdditionalUnitInterface $productAdditionalUnit);

    /**
     * @return Collection|ProductAdditionalUnitInterface[]
     */
    public function getAdditionalUnits();

    /**
     * @param string $identifier
     *
     * @return ProductAdditionalUnitInterface|null
     */
    public function getAdditionalUnit(string $identifier);

    /**
     * @return ProductInterface
     */
    public function getProduct();

    /**
     * @param ProductInterface $product
     */
    public function setProduct(ProductInterface $product);
}
