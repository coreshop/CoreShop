<?php

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
