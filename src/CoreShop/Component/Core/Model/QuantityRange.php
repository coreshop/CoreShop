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
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRange as BaseQuantityRange;

class QuantityRange extends BaseQuantityRange implements QuantityRangeInterface
{
    /**
     * @var int
     */
    protected $amount = 0;

    /**
     * @var CurrencyInterface|null
     */
    protected $currency;

    /**
     * @var ProductUnitDefinitionInterface|null
     */
    protected $unitDefinition;

    /**
     * @var int
     */
    protected $pseudoPrice = 0;

    /**
     * {@inheritdoc}
     */
    public function getAmount()
    {
        return (int) $this->amount;
    }

    /**
     * {@inheritdoc}
     */
    public function setAmount(int $amount)
    {
        $this->amount = $amount;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency(CurrencyInterface $currency = null)
    {
        $this->currency = $currency;
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
    public function setUnitDefinition(ProductUnitDefinitionInterface $unitDefinition = null)
    {
        $this->unitDefinition = $unitDefinition;
    }

    /**
     * {@inheritdoc}
     */
    public function hasUnitDefinition()
    {
        return $this->unitDefinition instanceof ProductUnitDefinitionInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function getPseudoPrice()
    {
        return (int) $this->pseudoPrice;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPseudoPrice()
    {
        return $this->getPseudoPrice() !== 0;
    }

    /**
     * {@inheritdoc}
     */
    public function setPseudoPrice(int $pseudoPrice)
    {
        $this->pseudoPrice = $pseudoPrice;
    }
}
