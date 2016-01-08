<?php
/**
 * Pimcore
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @category   Pimcore
 * @package    Object
 * @copyright  Copyright (c) 2009-2015 pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model;

use Pimcore\Model;
use Pimcore\Tool;

class LocalizedFields extends Model\AbstractModel {

    const STRICT_DISABLED = 0;

    const STRICT_ENABLED = 1;

    private static $getFallbackValues = false;

    /**
     * @var array
     */
    public $items = array();

    /**
     * @var array
     */
    public $fields = array();

    /**
     * @var AbstractModel
     */
    public $object;

    /**
     * @var bool
     */
    private static $strictMode;

    /**
     * @param boolean $getFallbackValues
     */
    public static function setGetFallbackValues($getFallbackValues)
    {
        self::$getFallbackValues = $getFallbackValues;
    }

    /**
     * @return boolean
     */
    public static function getGetFallbackValues()
    {
        return self::$getFallbackValues;
    }

    /**
     * @return boolean
     */
    public static function isStrictMode()
    {
        return self::$strictMode;
    }

    /**
     * @param boolean $strictMode
     */
    public static function setStrictMode($strictMode)
    {
        self::$strictMode = $strictMode;
    }


    /**
     * @return boolean
     */
    public static function doGetFallbackValues()
    {
        return self::$getFallbackValues;
    }

    /**
     * @param array $items
     */
    public function __construct($fields, $items = null) {
        if($items) {
            $this->setItems($items);
        }

        $this->setFields($fields);
    }

    /**
     * @param  $item
     * @return void
     */
    public function addItem($item)
    {
        $this->items[] = $item;
    }

    /**
     * @param  array $items
     * @return void
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
     * @param  array fields
     * @return void
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
     * @return void
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
     * @throws \Exception
     * @param null $language
     * @return string
     */
    public function getLanguage ($language = null) {
        if($language) {
            return (string) $language;
        }

        // try to get the language from the registry
        try {
            $locale = \Zend_Registry::get("Zend_Locale");
            if(Tool::isValidLanguage((string) $locale)) {
                return (string) $locale;
            }
            throw new \Exception("Not supported language");
        } catch (\Exception $e) {
            return Tool::getDefaultLanguage();
        }
    }

    /**
     * @param $language
     * @return bool
     */
    public function languageExists ($language) {
        return array_key_exists($language, $this->getItems());
    }

    /**
     * @param $name
     * @param null $language
     * @return
     */
    public function getLocalizedValue ($name, $language = null, $ignoreFallbackLanguage = false) {

        $data = null;
        $language = $this->getLanguage($language);

        if($this->languageExists($language)) {
            if(array_key_exists($name, $this->items[$language])) {
                $data = $this->items[$language][$name];
            }
        }

        // check for fallback value
        if(!$data && !$ignoreFallbackLanguage && self::doGetFallbackValues()) {
            foreach (Tool::getFallbackLanguagesFor($language) as $l) {
                if($this->languageExists($l)) {
                    if(array_key_exists($name, $this->items[$l])) {
                        $data = $this->getLocalizedValue($name, $l);
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @param $name
     * @param $value
     * @param null $language
     * @return void
     * @throws \Exception
     */
    public function setLocalizedValue ($name, $value, $language = null) {

        if (self::$strictMode) {
            if (!$language || !in_array($language, Tool::getValidLanguages())) {
                throw new \Exception("Language " . $language . " not accepted in strict mode");
            }
        }

        $language  = $this->getLanguage($language);
        if(!$this->languageExists($language)) {
            $this->items[$language] = array();
        }

        $this->items[$language][$name] = $value;
    }

    /**
     * @return array
     */
    public function __sleep() {
        return array("items");
    }
}
