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

namespace CoreShop\Component\Currency\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface ExchangeRateInterface extends ResourceInterface
{
    /**
     * @return float
     */
    public function getExchangeRate();

    /**
     * @param float $exchangeRate
     */
    public function setExchangeRate($exchangeRate);

    /**
     * @return CurrencyInterface
     */
    public function getFromCurrency();

    /**
     * @param CurrencyInterface $currency
     */
    public function setFromCurrency(CurrencyInterface $currency);

    /**
     * @return CurrencyInterface
     */
    public function getToCurrency();

    /**
     * @param CurrencyInterface $currency
     */
    public function setToCurrency(CurrencyInterface $currency);
}
