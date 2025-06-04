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
use CoreShop\Behat\Page\Frontend\CategoryPageInterface;
use Webmozart\Assert\Assert;

final class FilterContext implements Context
{
    public function __construct(
        private CategoryPageInterface $categoryPage,
    ) {
    }

    /**
     * @Then /^I should see a filter with label "([^"]+)"$/
     */
    public function iShouldSeeFilter(string $label): void
    {
        Assert::same(strtolower($this->categoryPage->getFilterLabel()), strtolower($label));
    }

    /**
     * @Then /^I select filter option "([^"]+)"$/
     */
    public function iSelectOption(string $label): void
    {
        $this->categoryPage->iSelectFilterOption($label);
        $this->categoryPage->clickFilterSubmit();
    }

    /**
     * @Then /^I type in search field "([^"]+)"$/
     */
    public function iTypeinSearchField(string $query): void
    {
        $this->categoryPage->setSearchField($query);
        $this->categoryPage->clickFilterSubmit();
    }
}
