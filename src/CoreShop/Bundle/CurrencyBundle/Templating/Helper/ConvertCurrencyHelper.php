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

namespace CoreShop\Bundle\CurrencyBundle\Templating\Helper;

use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use Symfony\Component\Templating\Helper\Helper;

class ConvertCurrencyHelper extends Helper implements ConvertCurrencyHelperInterface
{
    private $currencyConverter;

    public function __construct(CurrencyConverterInterface $currencyConverter)
    {
        $this->currencyConverter = $currencyConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function convertAmount(?int $amount, string $sourceCurrencyCode, string $targetCurrencyCode): int
    {
        if (null === $amount) {
            return 0;
        }

        return $this->currencyConverter->convert($amount, $sourceCurrencyCode, $targetCurrencyCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'coreshop_currency_converter';
    }
}
