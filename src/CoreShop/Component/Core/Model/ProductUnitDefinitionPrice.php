<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Core\Model;

/**
 * @psalm-suppress MissingConstructor
 */
class ProductUnitDefinitionPrice extends \CoreShop\Component\Product\Model\ProductUnitDefinitionPrice implements ProductUnitDefinitionPriceInterface
{
    /**
     * @var ProductStoreValuesInterface
     */
    protected $productStoreValues;

    public function getProductStoreValues()
    {
        return $this->productStoreValues;
    }

    /**
     * @return void
     */
    public function setProductStoreValues(ProductStoreValuesInterface $productStoreValues)
    {
        $this->productStoreValues = $productStoreValues;
    }
}
