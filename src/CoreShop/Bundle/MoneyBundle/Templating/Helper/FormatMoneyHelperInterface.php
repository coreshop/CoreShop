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

namespace CoreShop\Bundle\MoneyBundle\Templating\Helper;

interface FormatMoneyHelperInterface
{
    /**
     * @param int    $amount
     * @param string $currencyCode
     * @param string $localeCode
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function formatAmount(int $amount, string $currencyCode, string $localeCode): string;
}
