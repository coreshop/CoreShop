<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface ProductUnitDefinitionInterface extends ResourceInterface
{
    /**
     * @return ProductUnitInterface
     */
    public function getUnit();

    /**
     * @param ProductUnitInterface $unit
     */
    public function setUnit(ProductUnitInterface $unit);

    /**
     * @return float|null
     */
    public function getConversionRate();

    /**
     * @param float $conversionRate
     */
    public function setConversionRate(float $conversionRate = null);

    /**
     * @return int
     */
    public function getPrecision();

    /**
     * @param int $precision
     */
    public function setPrecision(int $precision);

    /**
     * @return ProductUnitDefinitionsInterface|null
     */
    public function getProductUnitDefinitions();

    /**
     * @param ProductUnitDefinitionsInterface|null $productUnitDefinitions
     */
    public function setProductUnitDefinitions(ProductUnitDefinitionsInterface $productUnitDefinitions = null);

    /**
     * @return string|null
     */
    public function getUnitName();
}
