<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\OrderBundle\Pimcore\GridColumnConfig\Operator\Factory;

use CoreShop\Bundle\OrderBundle\Pimcore\GridColumnConfig\Operator\PriceFormatter;
use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use Pimcore\DataObject\GridColumnConfig\Operator\Factory\OperatorFactoryInterface;
use Pimcore\DataObject\GridColumnConfig\Operator\OperatorInterface;

class PriceFormatterFactory implements OperatorFactoryInterface
{
    /**
     * @var MoneyFormatterInterface
     */
    private $moneyFormatter;

    /**
     * @var LocaleContextInterface
     */
    private $localeService;

    /**
     * @param MoneyFormatterInterface $moneyFormatter
     * @param LocaleContextInterface $localeService
     */
    public function __construct(
        MoneyFormatterInterface $moneyFormatter,
        LocaleContextInterface $localeService
    )
    {
        $this->moneyFormatter = $moneyFormatter;
        $this->localeService = $localeService;
    }

    /**
     * @param \stdClass $configElement
     * @param null $context
     * @return OperatorInterface
     */
    public function build(\stdClass $configElement, $context = null): OperatorInterface
    {
        return new PriceFormatter($this->moneyFormatter, $this->localeService, $configElement, $context);
    }
}
