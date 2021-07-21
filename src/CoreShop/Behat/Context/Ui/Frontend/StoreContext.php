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

namespace CoreShop\Behat\Context\Ui\Frontend;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Behat\Service\StoreContextSetterInterface;
use CoreShop\Component\Store\Model\StoreInterface;

final class StoreContext implements Context
{
    private SharedStorageInterface $sharedStorage;
    private StoreContextSetterInterface $storeContextSetter;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        StoreContextSetterInterface $storeContextSetter
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->storeContextSetter = $storeContextSetter;
    }

    /**
     * @Given /^I changed (?:|back )my current (store to "([^"]+)")$/
     * @When /^I change (?:|back )my current (store to "([^"]+)")$/
     */
    public function iChangeMyCurrentStoreTo(StoreInterface $store): void
    {
        $this->storeContextSetter->setStore($store);
    }
}
