<?php

namespace CoreShop\Component\Resource\Translation;

use CoreShop\Component\Resource\Model\TranslatableInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
interface TranslatableEntityLocaleAssignerInterface
{
    /**
     * @param TranslatableInterface $translatableEntity
     */
    public function assignLocale(TranslatableInterface $translatableEntity);
}
