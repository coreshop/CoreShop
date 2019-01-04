<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Translation;

use CoreShop\Component\Resource\Model\TranslatableInterface;
use CoreShop\Component\Resource\Translation\TranslatableEntityLocaleAssignerInterface;
use Pimcore\Cache\Runtime;
use Pimcore\Localization\LocaleServiceInterface;
use Pimcore\Tool;

final class TranslatableEntityPimcoreLocaleAssigner implements TranslatableEntityLocaleAssignerInterface
{
    /**
     * @var LocaleServiceInterface
     */
    private $pimcoreServiceLocale;

    /**
     * @param LocaleServiceInterface $pimcoreServiceLocale
     */
    public function __construct(LocaleServiceInterface $pimcoreServiceLocale)
    {
        $this->pimcoreServiceLocale = $pimcoreServiceLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function assignLocale(TranslatableInterface $translatableEntity)
    {
        $translatableEntity->setCurrentLocale($this->getPimcoreLanguage());
        $translatableEntity->setFallbackLocale(Tool::getDefaultLanguage());
    }

    /**
     * @return null|string
     */
    private function getPimcoreLanguage()
    {
        $locale = null;

        if (Runtime::isRegistered('model.locale')) {
            $locale = Runtime::get('model.locale');
        }

        if (null === $locale) {
            $locale = $this->pimcoreServiceLocale->findLocale();
        }

        if (Tool::isValidLanguage($locale)) {
            return (string) $locale;
        }

        return Tool::getDefaultLanguage();
    }
}
