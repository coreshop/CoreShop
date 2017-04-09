<?php

namespace CoreShop\Bundle\CurrencyBundle\Templating\Helper;

use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use Symfony\Component\Templating\Helper\Helper;

class ConvertMoneyHelper extends Helper implements ConvertMoneyHelperInterface
{
    /**
     * @var CurrencyConverterInterface
     */
    private $currencyConverter;

    /**
     * @param CurrencyConverterInterface $currencyConverter
     */
    public function __construct(CurrencyConverterInterface $currencyConverter)
    {
        $this->currencyConverter = $currencyConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function convertAmount($amount, $sourceCurrencyCode, $targetCurrencyCode)
    {
        return $this->currencyConverter->convert($amount, $sourceCurrencyCode, $targetCurrencyCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'coreshop_money_converter';
    }
}
