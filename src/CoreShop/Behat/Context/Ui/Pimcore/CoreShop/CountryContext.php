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

namespace CoreShop\Behat\Context\Ui\Pimcore\CoreShop;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Page\Pimcore\CoreShop\CountryPageInterface;
use CoreShop\Behat\Page\Pimcore\PWAPageInterface;
use Webmozart\Assert\Assert;

final class CountryContext implements Context
{
    public function __construct(
        private PWAPageInterface $pwaPage,
        private CountryPageInterface $countryPage,
    ) {
    }

    /**
     * @When I open the countries tab
     */
    public function iOpenTheCountriesTab(): void
    {
        $this->pwaPage->openResource('coreshop.address', 'country');
    }

    /**
     * @When countries tab is open
     */
    public function countriesTabIsOpen(): void
    {
        Assert::true($this->countryPage->isActiveOpen());
    }

    /**
     * @Given /^I create a new country named "([^"]+)"$/
     */
    public function createNewCountry($name): void
    {
        $this->countryPage->create($name);
    }
}
