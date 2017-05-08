<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
*/

namespace CoreShop\Component\Currency\Converter;

use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Currency\Repository\CurrencyRepositoryInterface;

final class CurrencyConverter implements CurrencyConverterInterface
{
    /**
     * @var CurrencyRepositoryInterface
     */
    private $currencyRepository;

    /**
     * @param CurrencyRepositoryInterface $currencyRepository
     */
    public function __construct(CurrencyRepositoryInterface $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function convert($amount, $sourceCurrencyCode, $targetCurrencyCode)
    {
        if ($sourceCurrencyCode === $targetCurrencyCode) {
            return $amount;
        }

        /**
         * @var $sourceCurrency CurrencyInterface
         */
        $sourceCurrency = $this->currencyRepository->getByCode($sourceCurrencyCode);
        /**
         * @var $targetCurrencyCode CurrencyInterface
         */
        $targetCurrency = $this->currencyRepository->getByCode($targetCurrencyCode);

        return $amount * $targetCurrency->getExchangeRate();
    }
}
