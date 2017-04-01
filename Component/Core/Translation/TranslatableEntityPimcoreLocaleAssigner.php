<?php

namespace CoreShop\Component\Core\Translation;

use CoreShop\Component\Resource\Model\TranslatableInterface;
use CoreShop\Component\Resource\Translation\TranslatableEntityLocaleAssignerInterface;

final class TranslatableEntityPimcoreLocaleAssigner implements TranslatableEntityLocaleAssignerInterface
{
    /**
     * {@inheritdoc}
     */
    public function assignLocale(TranslatableInterface $translatableEntity)
    {
        $translatableEntity->setCurrentLocale(\Pimcore\Tool::getDefaultLanguage());
        $translatableEntity->setFallbackLocale(\Pimcore\Tool::getDefaultLanguage());
    }
}
