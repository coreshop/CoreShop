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

namespace CoreShop\Component\Product\Rule\Action;

use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Currency\Repository\CurrencyRepositoryInterface;
use Webmozart\Assert\Assert;

class PriceActionProcessor implements ProductPriceActionProcessorInterface
{
    public function __construct(protected CurrencyRepositoryInterface $currencyRepository, protected CurrencyConverterInterface $moneyConverter)
    {
    }

    public function getPrice($subject, array $context, array $configuration): int
    {
        Assert::keyExists($context, 'currency');
        Assert::isInstanceOf($context['currency'], CurrencyInterface::class);

        /**
         * @var CurrencyInterface $contextCurrency
         */
        $contextCurrency = $context['currency'];
        $price = $configuration['price'];

        /**
         * @var CurrencyInterface $currency
         */
        $currency = $this->currencyRepository->find($configuration['currency']);

        Assert::isInstanceOf($currency, CurrencyInterface::class);

        return $this->moneyConverter->convert($price, $currency->getIsoCode(), $contextCurrency->getIsoCode());
    }
}
