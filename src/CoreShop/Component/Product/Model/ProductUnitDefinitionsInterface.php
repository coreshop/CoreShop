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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\Collections\Collection;

interface ProductUnitDefinitionsInterface extends ResourceInterface
{
    public function getId(): ?int;

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
    public function getUnitDefinition(string $identifier);

    public function addAdditionalUnitDefinition(ProductUnitDefinitionInterface $unitDefinition);

    public function removeAdditionalUnitDefinition(ProductUnitDefinitionInterface $unitDefinition);

    /**
     * @return Collection|ProductUnitDefinitionInterface[]
     */
    public function getAdditionalUnitDefinitions();
}
