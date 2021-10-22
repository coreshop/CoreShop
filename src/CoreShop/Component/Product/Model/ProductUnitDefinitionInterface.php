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

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface ProductUnitDefinitionInterface extends ResourceInterface
{
    /**
     * @return ProductUnitInterface|null
     */
    public function getUnit();

    public function setUnit(ProductUnitInterface $unit);

    /**
     * @return float|null
     */
    public function getConversionRate();

    public function setConversionRate(float $conversionRate = null);

    /**
     * @return int
     */
    public function getPrecision();

    public function setPrecision(int $precision);

    /**
     * @return ProductUnitDefinitionsInterface|null
     */
    public function getProductUnitDefinitions();

    public function setProductUnitDefinitions(ProductUnitDefinitionsInterface $productUnitDefinitions = null);

    /**
     * @return string|null
     */
    public function getUnitName();
}
