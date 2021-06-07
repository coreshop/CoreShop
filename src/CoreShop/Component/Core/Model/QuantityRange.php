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

    public function getAmount()
    {
        return (int) $this->amount;
    }

    public function setAmount(int $amount)
    {
        $this->amount = $amount;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency(CurrencyInterface $currency = null)
    {
        $this->currency = $currency;
    }

    public function getUnitDefinition()
    {
        return $this->unitDefinition;
    }

    public function setUnitDefinition(ProductUnitDefinitionInterface $unitDefinition = null)
    {
        $this->unitDefinition = $unitDefinition;
    }

    public function hasUnitDefinition()
    {
        return $this->unitDefinition instanceof ProductUnitDefinitionInterface;
    }

    public function getPseudoPrice()
    {
        return (int) $this->pseudoPrice;
    }

    public function hasPseudoPrice()
    {
        return $this->getPseudoPrice() !== 0;
    }

    public function setPseudoPrice(int $pseudoPrice)
    {
        $this->pseudoPrice = $pseudoPrice;
    }

    public function __clone()
    {
        parent::__clone();

        if ($this->unitDefinition === null) {
            return;
        }

        $this->unitDefinition = null;
    }
}
