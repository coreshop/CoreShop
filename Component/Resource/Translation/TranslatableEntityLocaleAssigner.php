<?php

namespace CoreShop\Component\Resource\Translation;

use CoreShop\Component\Resource\Model\TranslatableInterface;
use CoreShop\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;

final class TranslatableEntityLocaleAssigner implements TranslatableEntityLocaleAssignerInterface
{
    /**
     * @var TranslationLocaleProviderInterface
     */
    private $translationLocaleProvider;

    /**
     * @param TranslationLocaleProviderInterface $translationLocaleProvider
     */
    public function __construct(TranslationLocaleProviderInterface $translationLocaleProvider)
    {
        $this->translationLocaleProvider = $translationLocaleProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function assignLocale(TranslatableInterface $translatableEntity)
    {
        $localeCode = $this->translationLocaleProvider->getDefaultLocaleCode();

        $translatableEntity->setCurrentLocale($localeCode);
        $translatableEntity->setFallbackLocale($localeCode);
    }
}
