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

namespace CoreShop\Bundle\CurrencyBundle\Twig;

use Symfony\Component\Intl\Currencies;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class CurrencyExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('coreshop_currency_symbol', [$this, 'convertCurrencyCodeToSymbol']),
        ];
    }

    public function convertCurrencyCodeToSymbol(string $code, ?string $locale = null): string
    {
        return Currencies::getSymbol($code);
    }
}
