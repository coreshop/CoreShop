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

namespace CoreShop\Bundle\MoneyBundle\Formatter;

use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use Webmozart\Assert\Assert;

final class MoneyFormatter implements MoneyFormatterInterface
{
    private int $decimalFactor;

    public function __construct(int $decimalFactor)
    {
        $this->decimalFactor = $decimalFactor;
    }

    public function format(int $amount, string $currency, string $locale = 'en', int $fraction = 2, int $factor = null): string
    {
        $formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
        $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, $fraction);

        $result = $formatter->formatCurrency(abs($amount / ($factor ?? $this->decimalFactor)), $currency);
        Assert::notSame(
            false,
            $result,
            sprintf('The amount "%s" of type %s cannot be formatted to currency "%s".', $amount, gettype($amount), $currency)
        );

        return $amount >= 0 ? $result : '-' . $result;
    }
}
