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

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getPrice()
    {
        return $this->price;
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

    public function __toString(): string
    {
        $definitionId = $this->getUnitDefinition() instanceof ProductUnitDefinitionInterface ? $this->getUnitDefinition()->getUnitName() : '--';

        return sprintf('Price for %s: %d', $definitionId, $this->getPrice());
    }
}
