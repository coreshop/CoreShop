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

use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
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
     * @return ProductUnitDefinitionInterface
     */
    public function getDefaultUnitDefinition();

    /**
     * @param ProductUnitDefinitionInterface $defaultUnitDefinition
     */
    public function setDefaultUnitDefinition(ProductUnitDefinitionInterface $defaultUnitDefinition);

    /**
     * @param ProductUnitDefinitionInterface $productUnitDefinition
     */
    public function addUnitDefinition(ProductUnitDefinitionInterface $productUnitDefinition);

    /**
     * @param ProductUnitDefinitionInterface $productUnitDefinition
     */
    public function removeUnitDefinition(ProductUnitDefinitionInterface $productUnitDefinition);

    /**
     * @return Collection|ProductUnitDefinitionInterface[]
     */
    public function getUnitDefinitions();

    /**
     * @param string $identifier
     *
     * @return ProductUnitDefinitionInterface|null
     */
    public function getUnitDefinition(string $identifier);

    /**
     * @return ProductInterface
     */
    public function getProduct();

    /**
     * @param ProductInterface $product
     */
    public function setProduct(ProductInterface $product);
}
