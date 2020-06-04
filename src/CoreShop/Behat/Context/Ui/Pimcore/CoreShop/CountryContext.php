<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Behat\Context\Ui\Pimcore\CoreShop;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Page\Pimcore\CoreShop\CountryPageInterface;
use CoreShop\Behat\Page\Pimcore\CoreShop\CurrencyPageInterface;
use CoreShop\Behat\Page\Pimcore\PWAPageInterface;
use Webmozart\Assert\Assert;

final class CountryContext implements Context
{
    private $pwaPage;
    private $countryPage;

    public function __construct(
        PWAPageInterface $pwaPage,
        CountryPageInterface $countryPage
    )
    {
        $this->pwaPage = $pwaPage;
        $this->countryPage = $countryPage;
    }

    /**
     * @When countries tab is open
     */
    public function countriesTabIsOpen(): void
    {
        Assert::true($this->countryPage->isActiveOpen());
    }
}
