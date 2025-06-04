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
        /**
         * @var string|null $pimcoreLocale
         *
         * @psalm-var string|null $pimcoreLocale
         */
        $pimcoreLocale = $this->pimcoreLocaleService->findLocale();

        if (null === $pimcoreLocale) {
            throw new LocaleNotFoundException();
        }

        if (!Tool::isValidLanguage($pimcoreLocale)) {
            return Tool::getDefaultLanguage();
        }

        return $pimcoreLocale;
    }
}
