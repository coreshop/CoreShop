<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CurrencyBundle\Twig;

use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class ConvertCurrencyExtension extends AbstractExtension
{
    public function __construct(private CurrencyConverterInterface $currencyConverter)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('coreshop_convert_currency', [$this, 'convertAmount']),
        ];
    }

    public function convertAmount(?int $amount, string $sourceCurrencyCode, string $targetCurrencyCode): int
    {
        if (null === $amount) {
            return 0;
        }

        return $this->currencyConverter->convert($amount, $sourceCurrencyCode, $targetCurrencyCode);
    }
}
