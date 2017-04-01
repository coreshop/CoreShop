<?php

namespace CoreShop\Component\Resource\Model;

interface TranslationInterface
{
    /**
     * @return TranslatableInterface
     */
    public function getTranslatable();

    /**
     * @param null|TranslatableInterface $translatable
     */
    public function setTranslatable(TranslatableInterface $translatable = null);

    /**
     * @return string
     */
    public function getLocale();

    /**
     * @param string $locale
     */
    public function setLocale($locale);
}
