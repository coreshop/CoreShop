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

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Component\Locale\Context\FixedLocaleContext;

final class LocaleContext implements Context
{
    public function __construct(
        private FixedLocaleContext $fixedLocaleContext,
    ) {
    }

    /**
     * @Given /^the site operates on locale "([^"]+)"$/
     */
    public function setLocale($locale): void
    {
        $this->fixedLocaleContext->setLocale($locale);
    }
}
