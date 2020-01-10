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

class ProductUnitDefinitionPrice extends \CoreShop\Component\Product\Model\ProductUnitDefinitionPrice implements ProductUnitDefinitionPriceInterface
{
    /**
     * @var ProductStoreValuesInterface
     */
    protected $productStoreValues;

    /**
     * {@inheritdoc}
     */
    public function getProductStoreValues()
    {
        return $this->productStoreValues;
    }

    /**
     * {@inheritdoc}
     */
    public function setProductStoreValues(ProductStoreValuesInterface $productStoreValues)
    {
        $this->productStoreValues = $productStoreValues;
    }
}
