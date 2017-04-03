<?php

namespace CoreShop\Component\Resource\Translation\Provider;

use Pimcore\Tool;

final class PimcoreTranslationLocaleProvider implements TranslationLocaleProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinedLocalesCodes()
    {
        return Tool::getValidLanguages();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLocaleCode()
    {
        return Tool::getDefaultLanguage();
    }
}
