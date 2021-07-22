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

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangeInterface as BaseQuantityRangeInterface;

interface QuantityRangeInterface extends BaseQuantityRangeInterface
{
    /**
     * @return int
     */
    public function getAmount();

    /**
     * @param int $amount
     */
    public function setAmount(int $amount);

    /**
     * @return CurrencyInterface|null
     */
    public function getCurrency();

    /**
     * @param CurrencyInterface $currency
     */
    public function setCurrency(CurrencyInterface $currency = null);

    /**
     * @return ProductUnitDefinitionInterface|null
     */
    public function getUnitDefinition();

    /**
     * @param ProductUnitDefinitionInterface|null $unitDefinition
     */
    public function setUnitDefinition(ProductUnitDefinitionInterface $unitDefinition = null);

    /**
     * @return bool
     */
    public function hasUnitDefinition();

    /**
     * @param int $pseudoPrice
     */
    public function setPseudoPrice(int $pseudoPrice);

    /**
     * @return int
     */
    public function getPseudoPrice();

    /**
     * @return bool
     */
    public function hasPseudoPrice();
}
