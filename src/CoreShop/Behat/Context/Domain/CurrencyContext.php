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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use Webmozart\Assert\Assert;

final class CurrencyContext implements Context
{
    public function __construct(private CurrencyRepositoryInterface $currencyRepository, private CurrencyContextInterface $currencyContext, private MoneyFormatterInterface $moneyFormatter)
    {
    }

    /**
     * @Then /^the site should be using (currency "[^"]+")$/
     */
    public function theSiteShouldBeUsingCurrency(CurrencyInterface $currency): void
    {
        Assert::same(
            $currency->getId(),
            $this->currencyContext->getCurrency()->getId(),
            sprintf(
                'Given currency (%s) is different from actual currency(%s)',
                $currency->getIsoCode(),
                $this->currencyContext->getCurrency()->getIsoCode()
            )
        );
    }

    /**
     * @Then /^the (store "[^"]+") should have "([^"]+)" currencies$/
     */
    public function theStoreShouldHaveXCurrencies(StoreInterface $store, $countOfCurrencies): void
    {
        $validCurrencies = $this->currencyRepository->findActiveForStore($store);

        Assert::same(
            count($validCurrencies),
            (int) $countOfCurrencies,
            sprintf(
                'Found "%s" valid currencies instead of of "%s"',
                count($validCurrencies),
                (int) $countOfCurrencies
            )
        );
    }

    /**
     * @Then /^the amount "([^"]+)" of (currency "[^"]+") in language "([^"]+)" should be formatted "([^"]+)"$/
     */
    public function currencyShouldBeFormatted($amount, CurrencyInterface $currency, $locale, $shouldBeFormat): void
    {
        $format = $this->moneyFormatter->format((int) $amount, $currency->getIsoCode(), $locale);

        Assert::eq(
            $format,
            $shouldBeFormat,
            sprintf(
                'Given format "%s" is different from actual format "%s"',
                $shouldBeFormat,
                $format
            )
        );
    }
}
