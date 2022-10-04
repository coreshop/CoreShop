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

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\Model\AbstractResource;

/**
 * @psalm-suppress MissingConstructor
 */
class ProductUnitDefinitionPrice extends AbstractResource implements ProductUnitDefinitionPriceInterface, \Stringable
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $price = 0;

    /**
     * @var ProductUnitDefinitionInterface
     */
    protected $unitDefinition;

    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return void
     */
    public function setPrice(int $price)
    {
        $this->price = $price;
    }

    public function getUnitDefinition()
    {
        return $this->unitDefinition;
    }

    /**
     * @return void
     */
    public function setUnitDefinition(ProductUnitDefinitionInterface $unitDefinition)
    {
        $this->unitDefinition = $unitDefinition;
    }

    public function __toString(): string
    {
        $definitionId = $this->getUnitDefinition() instanceof ProductUnitDefinitionInterface ? $this->getUnitDefinition()->getUnitName() : '--';

        return sprintf('Price for %s: %d', $definitionId, $this->getPrice());
    }
}
