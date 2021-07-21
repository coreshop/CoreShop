<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\OrderBundle\Pimcore\GridColumnConfig\Operator\Factory;

use CoreShop\Bundle\OrderBundle\Pimcore\GridColumnConfig\Operator\PriceFormatter;
use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use Pimcore\DataObject\GridColumnConfig\Operator\Factory\OperatorFactoryInterface;

class PriceFormatterFactory implements OperatorFactoryInterface
{
    private MoneyFormatterInterface $moneyFormatter;
    private LocaleContextInterface $localeService;

    public function __construct(
        MoneyFormatterInterface $moneyFormatter,
        LocaleContextInterface $localeService
    ) {
        $this->moneyFormatter = $moneyFormatter;
        $this->localeService = $localeService;
    }

    public function build(\stdClass $configElement, array $context = []): PriceFormatter
    {
        return new PriceFormatter($this->moneyFormatter, $this->localeService, $configElement, $context);
    }
}
