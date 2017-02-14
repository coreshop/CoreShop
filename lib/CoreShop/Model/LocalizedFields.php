<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model;

use CoreShop\Exception;
use Pimcore\Model;
use Pimcore\Tool;

/**
 * Class LocalizedFields
 * @package CoreShop\Model
 */
class LocalizedFields extends Model\AbstractModel
{
    /**
     * @var bool
     */
    private static $getFallbackValues = false;

    /**
     * @var array
     */
    public $items = [];

    /**
     * @var array
     */
    public $fields = [];

    /**
     * @var AbstractModel
     */
    public $object;

    /**
     * @var bool
     */
    private static $strictMode;

    /**
     * @param bool $getFallbackValues
     */
    public static function setGetFallbackValues($getFallbackValues)
    {
        self::$getFallbackValues = $getFallbackValues;
    }

    /**
     * @return bool
     */
    public static function getGetFallbackValues()
    {
        return self::$getFallbackValues;
    }

    /**
     * @return bool
     */
    public static function isStrictMode()
    {
        return self::$strictMode;
    }

    /**
     * @param bool $strictMode
     */
    public static function setStrictMode($strictMode)
    {
        self::$strictMode = $strictMode;
    }

    /**
     * @return bool
     */
    public static function doGetFallbackValues()
    {
        return self::$getFallbackValues;
    }

    /**
     * LocalizedFields constructor.
     *
     * @param $fields
     * @param null $items
     */
    public function __construct($fields, $items = null)
    {
        if ($items) {
            $this->setItems($items);
        }

        $this->setFields($fields);
    }

    /**
     * @param  $item
     */
    public function addItem($item)
    {
        $this->items[] = $item;
    }

    /**
     * @param array $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param array $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param AbstractModel $object
     */
    public function setObject(AbstractModel $object)
    {
        $this->object = $object;
    }

    /**
     * @return AbstractModel
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @throws Exception
     *
     * @param null $language
     *
     * @return string
     */
    public function getLanguage($language = null)
    {
        if ($language) {
            return (string) $language;
        }

        // try to get the language from the registry
        try {
            $locale = \CoreShop::getTools()->getLocale();
            if (Tool::isValidLanguage((string) $locale)) {
                return (string) $locale;
            }
            throw new Exception('Not supported language');
        } catch (\Exception $e) {
            return Tool::getDefaultLanguage();
        }
    }

    /**
     * @param $language
     *
     * @return bool
     */
    public function languageExists($language)
    {
        return array_key_exists($language, $this->getItems());
    }

    /**
     * Get Localized Value.
     *
     * @param $name
     * @param null $language
     * @param bool $ignoreFallbackLanguage
     *
     * @return mixed
     */
    public function getLocalizedValue($name, $language = null, $ignoreFallbackLanguage = false)
    {
        $data = null;
        $language = $this->getLanguage($language);

        if ($this->languageExists($language)) {
            if (array_key_exists($name, $this->items[$language])) {
                $data = $this->items[$language][$name];
            }
        }

        // check for fallback value
        if (!$data && !$ignoreFallbackLanguage && self::doGetFallbackValues()) {
            foreach (Tool::getFallbackLanguagesFor($language) as $l) {
                if ($this->languageExists($l)) {
                    if (array_key_exists($name, $this->items[$l])) {
                        $data = $this->getLocalizedValue($name, $l);
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Set Localized Value.
     *
     * @param $name
     * @param $value
     * @param null $language
     *
     * @throws Exception
     */
    public function setLocalizedValue($name, $value, $language = null)
    {
        if (self::$strictMode) {
            if (!$language || !in_array($language, Tool::getValidLanguages())) {
                throw new Exception('Language '.$language.' not accepted in strict mode');
            }
        }

        $language = $this->getLanguage($language);
        if (!$this->languageExists($language)) {
            $this->items[$language] = [];
        }

        $this->items[$language][$name] = $value;
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return ['items'];
    }
}
