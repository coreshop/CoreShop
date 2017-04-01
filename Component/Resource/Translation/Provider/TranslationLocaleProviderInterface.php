<?php

namespace CoreShop\Component\Resource\Translation\Provider;

interface TranslationLocaleProviderInterface
{
    /**
     * @return string[]
     */
    public function getDefinedLocalesCodes();

    /**
     * @return string
     */
    public function getDefaultLocaleCode();
}
