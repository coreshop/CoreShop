<?php

namespace CoreShop\Component\Core\Translation;

use CoreShop\Component\Resource\Model\TranslatableInterface;
use CoreShop\Component\Resource\Translation\TranslatableEntityLocaleAssignerInterface;
use Pimcore\Cache\Runtime;
use Pimcore\Service\Locale;
use Pimcore\Tool;

final class TranslatableEntityPimcoreLocaleAssigner implements TranslatableEntityLocaleAssignerInterface
{
    /**
     * @var Locale
     */
    private $pimcoreServiceLocale;

    /**
     * @param Locale $pimcoreServiceLocale
     */
    public function __construct(Locale $pimcoreServiceLocale)
    {
        $this->pimcoreServiceLocale = $pimcoreServiceLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function assignLocale(TranslatableInterface $translatableEntity)
    {
        $translatableEntity->setCurrentLocale($this->getPimcoreLanguage());
        $translatableEntity->setFallbackLocale(\Pimcore\Tool::getDefaultLanguage());
    }

    /**
     * @return null|string
     */
    private function getPimcoreLanguage() {
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
