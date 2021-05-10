<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Core\Translation;

use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Locale\Context\LocaleNotFoundException;
use CoreShop\Component\Resource\Model\TranslatableInterface;
use CoreShop\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use CoreShop\Component\Resource\Translation\TranslatableEntityLocaleAssignerInterface;

final class TranslatableEntityPimcoreLocaleAssigner implements TranslatableEntityLocaleAssignerInterface
{
    private $localeContext;
    private $translationLocaleProvider;

    public function __construct(
        LocaleContextInterface $localeContext,
        TranslationLocaleProviderInterface $translationLocaleProvider
    ) {
        $this->localeContext = $localeContext;
        $this->translationLocaleProvider = $translationLocaleProvider;
    }

    public function assignLocale(TranslatableInterface $translatableEntity): void
    {
        $fallbackLocale = $this->translationLocaleProvider->getDefaultLocaleCode();

        try {
            $currentLocale = $this->localeContext->getLocaleCode();
        } catch (LocaleNotFoundException $e) {
            $currentLocale = $fallbackLocale;
        }

        $translatableEntity->setCurrentLocale($currentLocale);
        $translatableEntity->setFallbackLocale($fallbackLocale);
    }
}
