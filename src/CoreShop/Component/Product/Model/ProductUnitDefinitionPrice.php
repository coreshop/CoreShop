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

declare(strict_types=1);

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
    protected $price = 0;

    /**
     * @var ProductUnitDefinitionInterface
     */
    protected $unitDefinition;

    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getPrice()
    {
        return (int) $this->price;
    }

    public function setPrice(int $price)
    {
        $this->price = $price;
    }

    public function getUnitDefinition()
    {
        return $this->unitDefinition;
    }

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
