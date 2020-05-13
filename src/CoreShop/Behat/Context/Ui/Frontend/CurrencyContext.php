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

namespace CoreShop\Behat\Context\Ui\Frontend;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Page\Frontend\HomePageInterface;
use CoreShop\Behat\Service\SharedStorageInterface;

final class CurrencyContext implements Context
{
    private $sharedStorage;
    private $homePage;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        HomePageInterface $homePage
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->homePage = $homePage;
    }

    /**
     * @When I switch to currency :currencyCode
     * @Given I changed my currency to :currencyCode
     */
    public function iSwitchTheCurrencyToTheCurrency($currencyCode)
    {
        $this->homePage->open();
        $this->homePage->switchCurrency($currencyCode);
    }
}
