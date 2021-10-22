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

use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Pimcore\Model\DataObject;

class LocalizedFieldGetter implements GetterInterface
{
    public function __construct(protected TranslationLocaleProviderInterface $localeProvider)
    {
    }

    public function get(IndexableInterface $object, IndexColumnInterface $config): array
    {
        $getter = 'get' . ucfirst($config->getObjectKey());

        $fallbackMemory = DataObject\Localizedfield::getGetFallbackValues();
        DataObject\Localizedfield::setGetFallbackValues(true);

        $values = [];
        foreach ($this->localeProvider->getDefinedLocalesCodes() as $locale) {
            $values[$locale] = $object->$getter($locale);
        }

        DataObject\Localizedfield::setGetFallbackValues($fallbackMemory);

        return $values;
    }
}
