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
