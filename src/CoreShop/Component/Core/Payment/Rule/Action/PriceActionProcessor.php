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

namespace CoreShop\Component\Core\Payment\Rule\Action;

use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Currency\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Payment\Model\PayableInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Payment\Rule\Action\ProviderPriceActionProcessorInterface;
use Webmozart\Assert\Assert;

class PriceActionProcessor implements ProviderPriceActionProcessorInterface
{
    public function __construct(
        protected CurrencyRepositoryInterface $currencyRepository,
        protected CurrencyConverterInterface $moneyConverter,
    ) {
    }

    public function getPrice(PaymentProviderInterface $paymentProvider, PayableInterface $payable, array $configuration, array $context): int
    {
        Assert::keyExists($context, 'base_currency');
        Assert::isInstanceOf($context['base_currency'], CurrencyInterface::class);

        /**
         * @var CurrencyInterface $contextCurrency
         */
        $contextCurrency = $context['base_currency'];
        $price = $configuration['price'];

        $currency = $this->currencyRepository->find($configuration['currency']);

        Assert::isInstanceOf($currency, CurrencyInterface::class);

        return $this->moneyConverter->convert($price, $currency->getIsoCode(), $contextCurrency->getIsoCode());
    }
}
