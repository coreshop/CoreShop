<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Behat\Context\Ui\Pimcore\CoreShop;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Page\Pimcore\CoreShop\TaxRuleGroupPageInterface;
use CoreShop\Behat\Page\Pimcore\PWAPageInterface;
use Webmozart\Assert\Assert;

final class TaxRuleGroupContext implements Context
{
    private PWAPageInterface $pwaPage;
    private TaxRuleGroupPageInterface $taxRuleGroupPage;

    public function __construct(
        PWAPageInterface $pwaPage,
        TaxRuleGroupPageInterface $taxRuleGroupPage
    )
    {
        $this->pwaPage = $pwaPage;
        $this->taxRuleGroupPage= $taxRuleGroupPage;
    }

    /**
     * @When tax-rule-groups tab is open
     */
    public function taxRuleGroupsTabIsOpen(): void
    {
        Assert::true($this->taxRuleGroupPage->isActiveOpen());
    }
}
