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

namespace CoreShop\Bundle\OrderBundle\Pimcore\GridColumnConfig\Operator;

use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use CoreShop\Component\Currency\Model\CurrencyAwareInterface;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Order\Model\SaleInterface;
use Pimcore\DataObject\GridColumnConfig\Operator\AbstractOperator;

class PriceFormatter extends AbstractOperator
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
     * @param LocaleContextInterface  $localeService
     * @param \stdClass               $config
     * @param null                    $context
     */
    public function __construct(
        MoneyFormatterInterface $moneyFormatter,
        LocaleContextInterface $localeService,
        \stdClass $config,
        $context = null
    ) {
        parent::__construct($config, $context);
        $this->moneyFormatter = $moneyFormatter;
        $this->localeService = $localeService;
    }

    /**
     * @param \Pimcore\Model\Element\ElementInterface $element
     *
     * @return null|\stdClass|string
     */
    public function getLabeledValue($element)
    {
        $result = new \stdClass();
        $result->label = $this->label;
        $children = $this->getChilds();

        if (!$children) {
            return $result;
        }

        $c = $children[0];
        $result = $c->getLabeledValue($element);

        if ($element instanceof CurrencyAwareInterface) {
            $currency = $element->getCurrency();
            $result->value = $this->moneyFormatter->format($result->value, $currency->getIsoCode(), $this->localeService->getLocaleCode());
        } elseif ($element instanceof SaleInterface) {
            $store = $element->getStore();
            $result->value = $this->moneyFormatter->format($result->value, $store->getCurrency()->getIsoCode(), $this->localeService->getLocaleCode());
        }

        return $result;
    }
}
