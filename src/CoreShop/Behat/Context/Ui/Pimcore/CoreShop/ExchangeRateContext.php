<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Behat\Context\Ui\Pimcore\CoreShop;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Page\Pimcore\CoreShop\ExchangeRatePageInterface;
use Webmozart\Assert\Assert;

final class ExchangeRateContext implements Context
{
    public function __construct(
        private ExchangeRatePageInterface $exchangeRatePage,
    ) {
    }

    /**
     * @When exchange-rates tab is open
     */
    public function exchangeRatesTabIsOpen(): void
    {
        Assert::true($this->exchangeRatePage->isActiveOpen());
    }
}
