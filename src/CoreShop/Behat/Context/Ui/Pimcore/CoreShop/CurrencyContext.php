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
use CoreShop\Behat\Page\Pimcore\CoreShop\CurrencyPageInterface;
use CoreShop\Behat\Page\Pimcore\PWAPageInterface;
use Webmozart\Assert\Assert;

final class CurrencyContext implements Context
{
    private $pwaPage;
    private $currencyPage;

    public function __construct(
        PWAPageInterface $pwaPage,
        CurrencyPageInterface $currencyPage
    )
    {
        $this->pwaPage = $pwaPage;
        $this->currencyPage = $currencyPage;
    }

    /**
     * @When currencies tab is open
     */
    public function currenciesTabIsOpen(): void
    {
        Assert::true($this->currencyPage->isActiveOpen());
    }
}
