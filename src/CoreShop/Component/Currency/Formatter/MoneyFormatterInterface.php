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

namespace CoreShop\Component\Currency\Formatter;

interface MoneyFormatterInterface
{
    /**
     * @param int    $amount
     * @param string $currencyCode
     * @param string $locale
     * @param int    $fraction
     * @param int    $factor
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function format(int $amount, string $currencyCode, string $locale = 'en', int $fraction = 2, int $factor = null): string;
}
