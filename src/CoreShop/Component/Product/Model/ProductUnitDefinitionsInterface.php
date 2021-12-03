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
use Doctrine\Common\Collections\Collection;

interface ProductUnitDefinitionsInterface extends ResourceInterface
{
    /**
     * @return ProductInterface
     */
    public function getProduct();

    public function setProduct(ProductInterface $product);

    /**
     * @return ProductUnitDefinitionInterface|null
     */
    public function getDefaultUnitDefinition();

    public function setDefaultUnitDefinition(ProductUnitDefinitionInterface $defaultUnitDefinition);

    public function addUnitDefinition(ProductUnitDefinitionInterface $productUnitDefinition);

    public function removeUnitDefinition(ProductUnitDefinitionInterface $productUnitDefinition);

    /**
     * @return Collection|ProductUnitDefinitionInterface[]
     */
    public function getUnitDefinitions();

    /**
     * @return bool
     */
    public function hasUnitDefinition(ProductUnitDefinitionInterface $unitDefinition);

    /**
     * @return ProductUnitDefinitionInterface|null
     */
    public function getUnitDefinition(?string $identifier);

    public function addAdditionalUnitDefinition(ProductUnitDefinitionInterface $unitDefinition);

    public function removeAdditionalUnitDefinition(ProductUnitDefinitionInterface $unitDefinition);

    /**
     * @return Collection|ProductUnitDefinitionInterface[]
     */
    public function getAdditionalUnitDefinitions();
}
