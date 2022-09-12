<?php
declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\OrderBundle\Pimcore\GridColumnConfig\Operator;

use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use CoreShop\Component\Currency\Model\CurrencyAwareInterface;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use Pimcore\DataObject\GridColumnConfig\Operator\AbstractOperator;

class PriceFormatter extends AbstractOperator
{
    public function __construct(
        private MoneyFormatterInterface $moneyFormatter,
        private LocaleContextInterface $localeService,
        \stdClass $config,
        $context = null,
    ) {
        parent::__construct($config, $context);
    }

    /**
     * @param \Pimcore\Model\Element\ElementInterface $element
     *
     * @return \stdClass|string|null
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
        } elseif ($element instanceof OrderInterface) {
            $store = $element->getStore();
            $result->value = $this->moneyFormatter->format($result->value, $store->getCurrency()->getIsoCode(), $this->localeService->getLocaleCode());
        }

        return $result;
    }
}
