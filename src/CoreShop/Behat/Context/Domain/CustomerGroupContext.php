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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use Webmozart\Assert\Assert;

final class CustomerGroupContext implements Context
{
    /**
     * @Then /^the (customer "[^"]+") should be in (customer-group "[^"]+")$/
     */
    public function theCustomerShouldBeInCustomerGroup(ProductInterface $product, CategoryInterface $category): void
    {
        Assert::oneOf($category, $product->getCategories());
    }
}
