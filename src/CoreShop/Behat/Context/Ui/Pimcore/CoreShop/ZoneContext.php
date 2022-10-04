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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Behat\Context\Ui\Pimcore\CoreShop;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Page\Pimcore\CoreShop\ZonePageInterface;
use Webmozart\Assert\Assert;

final class ZoneContext implements Context
{
    public function __construct(
        private ZonePageInterface $zonePage,
    ) {
    }

    /**
     * @When zones tab is open
     */
    public function zonesTabIsOpen(): void
    {
        Assert::true($this->zonePage->isActiveOpen());
    }
}
