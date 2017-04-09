<?php

namespace CoreShop\Bundle\CurrencyBundle\Formatter;

use Webmozart\Assert\Assert;

final class MoneyFormatter implements MoneyFormatterInterface
{
    /**
     * {@inheritdoc}
     */
    public function format($amount, $currency, $locale = 'en')
    {
        $formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);

        $result = $formatter->formatCurrency(abs($amount), $currency);
        Assert::notSame(
            false,
            $result,
            sprintf('The amount "%s" of type %s cannot be formatted to currency "%s".', $amount, gettype($amount), $currency)
        );

        return $amount >= 0 ? $result : '-' . $result;
    }
}
