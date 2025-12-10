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

namespace CoreShop\Component\Locale\Context;

final class FixedLocaleContext implements LocaleContextInterface
{
    private ?string $locale = null;

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getLocaleCode(): string
    {
        if ($this->locale) {
            return $this->locale;
        }

        throw new LocaleNotFoundException();
    }
}
