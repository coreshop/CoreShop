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
use CoreShop\Behat\Page\Pimcore\CoreShop\ExchangeRatePageInterface;
use Webmozart\Assert\Assert;

final class ExchangeRateContext implements Context
{
    public function __construct(private ExchangeRatePageInterface $exchangeRatePage)
    {
    }

    /**
     * @When exchange-rates tab is open
     */
    public function exchangeRatesTabIsOpen(): void
    {
        Assert::true($this->exchangeRatePage->isActiveOpen());
    }
}
