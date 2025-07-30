<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRange as BaseQuantityRange;

/**
 * @psalm-suppress MissingConstructor
 */
class QuantityRange extends BaseQuantityRange implements QuantityRangeInterface
{
    protected int $amount = 0;

    protected ?CurrencyInterface $currency = null;

    protected ?ProductUnitDefinitionInterface $unitDefinition = null;

    protected int $pseudoPrice = 0;

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount(int $amount)
    {
        $this->amount = $amount;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency(?CurrencyInterface $currency)
    {
        $this->currency = $currency;
    }

    public function getUnitDefinition()
    {
        return $this->unitDefinition;
    }

    public function setUnitDefinition(?ProductUnitDefinitionInterface $unitDefinition)
    {
        $this->unitDefinition = $unitDefinition;
    }

    public function hasUnitDefinition()
    {
        return $this->unitDefinition instanceof ProductUnitDefinitionInterface;
    }

    public function getPseudoPrice()
    {
        return $this->pseudoPrice;
    }

    public function hasPseudoPrice()
    {
        return null !== $this->getPseudoPrice() && $this->getPseudoPrice() !== 0;
    }

    public function setPseudoPrice(int $pseudoPrice)
    {
        $this->pseudoPrice = $pseudoPrice;
    }
}
