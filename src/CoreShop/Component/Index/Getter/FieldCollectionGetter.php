<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Index\Getter;

use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Pimcore\DataObject\LocaleFallbackHelper;
use CoreShop\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Localizedfield;

class FieldCollectionGetter implements GetterInterface
{
    public function __construct(protected TranslationLocaleProviderInterface $localeProvider)
    {
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
            $fd = $item->getDefinition()->getFieldDefinition($config->getObjectKey());

            if (!$fd) {
                continue;
            }

            if (!method_exists($item, $fieldGetter)) {
                continue;
            }

            if ($fd instanceof Localizedfield) {
                $fieldValues[] = LocaleFallbackHelper::useFallbackValues(function() use ($item, $fieldGetter) {
                    $values = [];

                    foreach ($this->localeProvider->getDefinedLocalesCodes() as $locale) {
                        $values[$locale] = $item->$fieldGetter($locale);
                    }

                    return $values;
                });
                continue;
            }

            $fieldValues[] = $item->$fieldGetter();
        }

        return count($fieldValues) > 0 ? $fieldValues : null;
    }
}
