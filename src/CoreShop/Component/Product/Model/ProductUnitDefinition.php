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

declare(strict_types=1);

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\Model\AbstractResource;
use Doctrine\Common\Collections\Collection;

class ProductUnitDefinition extends AbstractResource implements ProductUnitDefinitionInterface
{
    /**
     * @var int|null
     */
    protected $id;

    /**
     * @var ProductUnitInterface
     */
    protected $unit;

    /**
     * @var float
     */
    protected $conversionRate;

    /**
     * @var int
     */
    protected $precision = 0;

    /**
     * @var ProductUnitDefinitionsInterface
     */
    protected $productUnitDefinitions;

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

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
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * {@inheritdoc}
     */
    public function setUnit(ProductUnitInterface $unit)
    {
        $this->unit = $unit;
    }

    /**
     * {@inheritdoc}
     */
    public function getConversionRate()
    {
        return $this->conversionRate;
    }

    /**
     * {@inheritdoc}
     */
    public function setConversionRate(float $conversionRate = null)
    {
        $this->conversionRate = $conversionRate;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * {@inheritdoc}
     */
    public function setPrecision(int $precision)
    {
        $this->precision = $precision;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductUnitDefinitions()
    {
        return $this->productUnitDefinitions;
    }

    /**
     * {@inheritdoc}
     */
    public function setProductUnitDefinitions(ProductUnitDefinitionsInterface $productUnitDefinitions = null)
    {
        $this->productUnitDefinitions = $productUnitDefinitions;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnitName()
    {
        if ($unit = $this->getUnit()) {
            return $unit->getName();
        }

        return null;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s, (Conversion Rate: %s)', $this->getUnitName(), $this->getConversionRate());
    }

    public function __clone()
    {
        if ($this->id === null) {
            return;
        }

        $this->id = null;
    }
}
