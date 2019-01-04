<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Index\Getter;

use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Pimcore\Model\DataObject;

class LocalizedFieldGetter implements GetterInterface
{
    /**
     * @var TranslationLocaleProviderInterface
     */
    protected $localeProvider;

    /**
     * @param TranslationLocaleProviderInterface $localeProvider
     */
    public function __construct(TranslationLocaleProviderInterface $localeProvider)
    {
        $this->localeProvider = $localeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function get(IndexableInterface $object, IndexColumnInterface $config)
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
