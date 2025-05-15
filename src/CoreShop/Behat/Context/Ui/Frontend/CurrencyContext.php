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

namespace CoreShop\Behat\Context\Ui\Frontend;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Page\Frontend\HomePageInterface;

final class CurrencyContext implements Context
{
    public function __construct(
        private HomePageInterface $homePage,
    ) {
    }

    /**
     * @When I switch to currency :currencyCode
     *
     * @Given I changed my currency to :currencyCode
     */
    public function iSwitchTheCurrencyToTheCurrency($currencyCode): void
    {
        $this->homePage->open();
        $this->homePage->switchCurrency($currencyCode);
    }
}
