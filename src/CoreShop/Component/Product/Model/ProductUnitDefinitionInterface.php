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

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface ProductUnitDefinitionInterface extends ResourceInterface
{
    public function getId(): ?int;

    /**
     * @return ProductUnitInterface|null
     */
    public function getUnit();

    public function setUnit(ProductUnitInterface $unit);

    /**
     * @return float|null
     */
    public function getConversionRate();

    public function setConversionRate(?float $conversionRate);

    /**
     * @return int
     */
    public function getPrecision();

    public function setPrecision(int $precision);

    /**
     * @return ProductUnitDefinitionsInterface|null
     */
    public function getProductUnitDefinitions();

    public function setProductUnitDefinitions(?ProductUnitDefinitionsInterface $productUnitDefinitions);

    /**
     * @return string|null
     */
    public function getUnitName();
}
