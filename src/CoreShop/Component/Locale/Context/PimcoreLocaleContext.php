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

use Pimcore\Localization\LocaleServiceInterface;
use Pimcore\Tool;

class PimcoreLocaleContext implements LocaleContextInterface
{
    public function __construct(
        private LocaleServiceInterface $pimcoreLocaleService,
    ) {
    }

    public function getLocaleCode(): string
    {
        $pimcoreLocale = $this->pimcoreLocaleService->findLocale();

        if (!Tool::isValidLanguage($pimcoreLocale)) {
            return Tool::getDefaultLanguage();
        }

        return $pimcoreLocale;
    }
}
