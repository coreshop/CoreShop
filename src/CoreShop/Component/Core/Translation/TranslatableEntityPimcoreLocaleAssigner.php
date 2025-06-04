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

namespace CoreShop\Component\Core\Translation;

use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Locale\Context\LocaleNotFoundException;
use CoreShop\Component\Resource\Model\TranslatableInterface;
use CoreShop\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use CoreShop\Component\Resource\Translation\TranslatableEntityLocaleAssignerInterface;

final class TranslatableEntityPimcoreLocaleAssigner implements TranslatableEntityLocaleAssignerInterface
{
    public function __construct(
        private LocaleContextInterface $localeContext,
        private TranslationLocaleProviderInterface $translationLocaleProvider,
    ) {
    }

    public function assignLocale(TranslatableInterface $translatableEntity): void
    {
        $fallbackLocale = $this->translationLocaleProvider->getDefaultLocaleCode();

        try {
            $currentLocale = $this->localeContext->getLocaleCode();
        } catch (LocaleNotFoundException) {
            $currentLocale = $fallbackLocale;
        }

        $translatableEntity->setCurrentLocale($currentLocale);
        $translatableEntity->setFallbackLocale($fallbackLocale);
    }
}
