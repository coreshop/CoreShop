<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Resource\Translation;

use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Locale\Context\LocaleNotFoundException;
use CoreShop\Component\Resource\Model\TranslatableInterface;
use CoreShop\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;

final class TranslatableEntityLocaleAssigner implements TranslatableEntityLocaleAssignerInterface
{
    public function __construct(private LocaleContextInterface $localeContext, private TranslationLocaleProviderInterface $translationLocaleProvider)
    {
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
        $translatableEntity->setFallbackLocale($currentLocale);
    }
}
