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
use CoreShop\Behat\Page\Pimcore\CoreShop\TaxRuleGroupPageInterface;
use Webmozart\Assert\Assert;

final class TaxRuleGroupContext implements Context
{
    public function __construct(
        private TaxRuleGroupPageInterface $taxRuleGroupPage,
    ) {
    }

    /**
     * @When tax-rule-groups tab is open
     */
    public function taxRuleGroupsTabIsOpen(): void
    {
        Assert::true($this->taxRuleGroupPage->isActiveOpen());
    }
}
