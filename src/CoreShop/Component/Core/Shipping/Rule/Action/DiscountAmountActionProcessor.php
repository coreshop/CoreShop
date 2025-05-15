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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Core\Shipping\Rule\Action;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Currency\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Shipping\Rule\Action\CarrierPriceModificationActionProcessorInterface;
use Webmozart\Assert\Assert;

class DiscountAmountActionProcessor implements CarrierPriceModificationActionProcessorInterface
{
    public function __construct(
        protected CurrencyRepositoryInterface $currencyRepository,
        protected CurrencyConverterInterface $moneyConverter,
    ) {
    }

    public function getModification(CarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address, int $price, array $configuration, array $context): int
    {
        Assert::keyExists($context, 'base_currency');
        Assert::isInstanceOf($context['base_currency'], CurrencyInterface::class);

        /**
         * @var CurrencyInterface $contextCurrency
         */
        $contextCurrency = $context['base_currency'];
        $amount = $configuration['amount'];

        $currency = $this->currencyRepository->find($configuration['currency']);

        Assert::isInstanceOf($currency, CurrencyInterface::class);

        return -1 * $this->moneyConverter->convert($amount, $currency->getIsoCode(), $contextCurrency->getIsoCode());
    }
}
