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

namespace CoreShop\Behat\Context\Ui\Frontend;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\StoreContextSetterInterface;
use CoreShop\Component\Store\Model\StoreInterface;

final class StoreContext implements Context
{
    public function __construct(
        private StoreContextSetterInterface $storeContextSetter,
    ) {
    }

    /**
     * @Given /^I changed (?:|back )my current (store to "([^"]+)")$/
     *
     * @When /^I change (?:|back )my current (store to "([^"]+)")$/
     */
    public function iChangeMyCurrentStoreTo(StoreInterface $store): void
    {
        $this->storeContextSetter->setStore($store);
    }
}
