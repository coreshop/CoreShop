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

namespace CoreShop\Behat\Context\Ui\Pimcore\CoreShop;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Page\Pimcore\CoreShop\StatePageInterface;
use Webmozart\Assert\Assert;

final class StateContext implements Context
{
    public function __construct(
        private StatePageInterface $statePage,
    ) {
    }

    /**
     * @When states tab is open
     */
    public function statesTabIsOpen(): void
    {
        Assert::true($this->statePage->isActiveOpen());
    }
}
