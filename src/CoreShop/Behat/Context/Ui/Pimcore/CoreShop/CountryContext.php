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

namespace CoreShop\Behat\Context\Ui\Pimcore\CoreShop;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Page\Pimcore\CoreShop\CountryPageInterface;
use CoreShop\Behat\Page\Pimcore\PWAPageInterface;
use Webmozart\Assert\Assert;

final class CountryContext implements Context
{
    private PWAPageInterface $pwaPage;
    private CountryPageInterface $countryPage;

    public function __construct(
        PWAPageInterface $pwaPage,
        CountryPageInterface $countryPage
    )
    {
        $this->pwaPage = $pwaPage;
        $this->countryPage = $countryPage;
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
