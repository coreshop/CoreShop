<?php

namespace CoreShop\Component\Resource\Model;

interface TranslatableInterface
{
    /**
     * @return TranslationInterface[]
     */
    public function getTranslations();

    /**
     * @param string $locale
     *
     * @return TranslationInterface
     */
    public function getTranslation($locale = null);

    /**
     * @param TranslationInterface $translation
     *
     * @return bool
     */
    public function hasTranslation(TranslationInterface $translation);

    /**
     * @param TranslationInterface $translation
     */
    public function addTranslation(TranslationInterface $translation);

    /**
     * @param TranslationInterface $translation
     */
    public function removeTranslation(TranslationInterface $translation);

    /**
     * @param string $locale
     */
    public function setCurrentLocale($locale);

    /**
     * @param string $locale
     */
    public function setFallbackLocale($locale);
}
