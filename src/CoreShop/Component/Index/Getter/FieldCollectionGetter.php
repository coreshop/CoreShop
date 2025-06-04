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
use Pimcore\Model\DataObject\ClassDefinition\Data\Localizedfields;
use Pimcore\Model\DataObject\Fieldcollection;

class FieldCollectionGetter implements GetterInterface
{
    public function __construct(
        protected TranslationLocaleProviderInterface $localeProvider,
    ) {
    }

    public function get(IndexableInterface $object, IndexColumnInterface $config): mixed
    {
        $columnConfig = $config->getConfiguration();
        $fieldValues = [];
        $collectionField = $config->getGetterConfig()['collectionField'];

        $collectionContainerGetter = 'get' . ucfirst($collectionField);
        $collectionContainer = $object->$collectionContainerGetter();
        $validItems = [];
        $fieldGetter = 'get' . ucfirst($config->getObjectKey());

        if ($collectionContainer instanceof Fieldcollection) {
            foreach ($collectionContainer->getItems() as $item) {
                /**
                 * @psalm-var class-string $className
                 */
                $className = 'Pimcore\Model\DataObject\Fieldcollection\Data\\' . $columnConfig['className'];
                if (is_a($item, $className)) {
                    $validItems[] = $item;
                }
            }
        }

        foreach ($validItems as $item) {
            /**
             * @var Localizedfields|null $localizedFieldsFd
             */
            $localizedFieldsFd = $item->getDefinition()->getFieldDefinition('localizedfields');
            $fd = $item->getDefinition()->getFieldDefinition($config->getObjectKey());
            $localizedFd = $localizedFieldsFd?->getFieldDefinition($config->getObjectKey());

            if (!$fd) {
                continue;
            }

            if (!method_exists($item, $fieldGetter)) {
                continue;
            }

            if ($localizedFd) {
                LocaleFallbackHelper::useFallbackValues(function () use ($item, $fieldGetter, &$fieldValues) {
                    foreach ($this->localeProvider->getDefinedLocalesCodes() as $locale) {
                        $fieldValues[$locale][] = $item->$fieldGetter($locale);
                    }
                });

                continue;
            }

            $fieldValues[] = $item->$fieldGetter();
        }

        return count($fieldValues) > 0 ? $fieldValues : null;
    }
}
