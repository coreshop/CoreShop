<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Index\Getter;

use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Pimcore\DataObject\LocaleFallbackHelper;
use CoreShop\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;

class LocalizedFieldGetter implements GetterInterface
{
    public function __construct(
        protected TranslationLocaleProviderInterface $localeProvider,
    ) {
    }

    public function get(IndexableInterface $object, IndexColumnInterface $config): array
    {
        $getter = 'get' . ucfirst($config->getObjectKey());

        return LocaleFallbackHelper::useFallbackValues(function () use ($object, $getter) {
            $values = [];

            foreach ($this->localeProvider->getDefinedLocalesCodes() as $locale) {
                $values[$locale] = $object->$getter($locale);
            }

            return $values;
        });
    }
}
