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

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\Model\AbstractResource;

class ProductUnitDefinitionPrice extends AbstractResource implements ProductUnitDefinitionPriceInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $price;

    /**
     * @var ProductUnitDefinitionInterface
     */
    protected $unitDefinition;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice()
    {
        return (int)$this->price;
    }

    /**
     * {@inheritdoc}
     */
    public function setPrice(int $price)
    {
        $this->price = $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnitDefinition()
    {
        return $this->unitDefinition;
    }

    /**
     * {@inheritdoc}
     */
    public function setUnitDefinition(ProductUnitDefinitionInterface $unitDefinition)
    {
        $this->unitDefinition = $unitDefinition;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $definitionId = $this->getUnitDefinition() instanceof ProductUnitDefinitionInterface ? $this->getUnitDefinition()->getUnitName() : '--';
        return sprintf('Price for %s: %d', $definitionId, $this->getPrice());
    }
}
