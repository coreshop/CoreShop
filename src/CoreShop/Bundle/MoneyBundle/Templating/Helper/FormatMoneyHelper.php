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

namespace CoreShop\Bundle\MoneyBundle\Templating\Helper;

use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use Symfony\Component\Templating\Helper\Helper;

class FormatMoneyHelper extends Helper implements FormatMoneyHelperInterface
{
    private $moneyFormatter;

    public function __construct(MoneyFormatterInterface $moneyFormatter)
    {
        $this->moneyFormatter = $moneyFormatter;
    }

    /**
     * {@inheritdoc}
     */
    public function formatAmount(int $amount, string $currencyCode, string $localeCode): string
    {
        return $this->moneyFormatter->format($amount, $currencyCode, $localeCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'coreshop_format_money';
    }
}
