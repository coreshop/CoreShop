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

class ProductAdditionalUnit extends AbstractResource implements ProductAdditionalUnitInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * @var ProductUnitInterface
     */
    protected $unit;

    /**
     * @var integer
     */
    protected $precision;

    /**
     * @var float
     */
    protected $conversionRate;

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
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * {@inheritdoc}
     */
    public function setProduct(ProductInterface $product)
    {
        $this->product = $product;
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
    public function getConversionRate()
    {
        return $this->conversionRate;
    }

    /**
     * {@inheritdoc}
     */
    public function setConversionRate(float $conversionRate)
    {
        $this->conversionRate = $conversionRate;
    }
}
