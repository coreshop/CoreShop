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

namespace CoreShop\Bundle\CoreBundle\Product\Rule\Condition;

use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;

class CurrenciesConditionChecker implements ConditionCheckerInterface
{
    /**
     * @var CurrencyContextInterface
     */
    private $currencyContext;

    /**
     * @param CurrencyContextInterface $currencyContext
     */
    public function __construct(CurrencyContextInterface $currencyContext)
    {
        $this->currencyContext = $currencyContext;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($subject, array $configuration)
    {
        $currency = $this->currencyContext->getCurrency();

        if (!$currency instanceof CurrencyInterface) {
            return false;
        }

        return in_array($currency->getId(), $configuration['currencies']);
    }
}
