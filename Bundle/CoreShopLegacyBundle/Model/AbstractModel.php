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

namespace CoreShop\Bundle\CoreShopLegacyBundle\Model;

use CoreShop\Bundle\CoreShopLegacyBundle\Exception;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Listing\AbstractListing;
use CoreShop\Bundle\CoreShopLegacyBundle\Tools;
use Pimcore\Cache;
use Pimcore\File;
use Pimcore\Logger;
use Pimcore\Model;
use Pimcore\Tool;

/**
 * Class AbstractModel
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Model
 */
class AbstractModel extends Model\AbstractModel
{
    /**
     * @var int
     */
    public $id;

    /**
     * Array of all localized field names.
     *
     * @var array
     */
    protected $localizedValues = [];

    /**
     * Localized field Provider.
     *
     * @var LocalizedFields
     */
    protected $localizedFields;

    /**
     * Determines if the object has a 1:n relation for shops
     *
     * @var bool
     */
    protected static $isMultiShop = false;

    /**
     * Determines if the object has a 1:1 relation with shops
     *
     * @var bool
     */
    protected static $isMultiShopFK = false;

    /**
     * Get Range by id.
     *
     * @param $id
     *
     * @return null|static
     */
    public static function getById($id)
    {
        return static::getByShopId($id, null);
    }

    /**
     * @return boolean
     */
    public static function isMultiShop()
    {
        $class = get_called_class();

        if (\Pimcore::getDiContainer()->has($class)) {
            $class = \Pimcore::getDiContainer()->get($class);
        }

        return $class::$isMultiShop;
    }

    /**
     * @return boolean
     */
    public static function isMultiShopFK()
    {
        $class = get_called_class();

        if (\Pimcore::getDiContainer()->has($class)) {
            $class = \Pimcore::getDiContainer()->get($class);
        }

        return $class::$isMultiShopFK;
    }

    /**
     * Create new instance of Pimcore Object.
     *
     * @throws Exception
     *
     * @return static
     */
    public static function create()
    {
        return Tools::createObject(static::class);
    }

    /**
     * @param $id
     * @param null $shopId
     * @return static|null
     */
    public static function getByShopId($id, $shopId = null)
    {
        $id = intval($id);

        if ($id < 1) {
            return null;
        }

        $className = get_called_class();
        $cacheKey = self::getClassCacheKey($className, $id . ($shopId ? '_' . $shopId : ''));

        try {
            $object = \Zend_Registry::get($cacheKey);
            if (!$object) {
                throw new Exception($className.' in registry is null');
            }

            return $object;
        } catch (\Exception $e) {
            try {
                if (!$object = Cache::load($cacheKey)) {
                    $object = static::create();
                    $object->getDao()->getById($id, $shopId);

                    \Zend_Registry::set($cacheKey, $object);
                    Cache::save($object, $cacheKey, [$cacheKey, $object->getCacheKey()]);
                } else {
                    \Zend_Registry::set($cacheKey, $object);
                }

                return $object;
            } catch (\Exception $e) {
                Logger::warning($e->getMessage());
            }
        }

        return null;
    }

    /**
     * Get Range by id.
     *
     * @param string $field
     * @param string $value
     * @param int $shopId
     *
     * @return null|static
     */
    public static function getByField($field, $value, $shopId = null)
    {
        $className = get_called_class();
        $cacheKey = self::getClassCacheKey($className, md5($field . '_' . File::getValidFilename(str_replace('-', '_', $value))) . ($shopId ? $shopId : ''));

        try {
            $object = \Zend_Registry::get($cacheKey);
            if (!$object) {
                throw new Exception($className.' in registry is null');
            }

            return $object;
        } catch (\Exception $e) {
            try {
                if (!$object = Cache::load($cacheKey)) {
                    $object = static::create();
                    $object->getDao()->getByField($field, $value, $shopId);

                    \Zend_Registry::set($cacheKey, $object);
                    Cache::save($object, $cacheKey, [$object->getCacheKey()]);
                } else {
                    \Zend_Registry::set($cacheKey, $object);
                }

                return $object;
            } catch (\Exception $e) {
                Logger::warning($e->getMessage());
            }
        }

        return null;
    }

    /**
     * Get cache key for class.
     *
     * @param $className
     * @param $append string
     *
     * @return string
     */
    protected static function getClassCacheKey($className, $append)
    {
        return 'coreshop_'.str_replace('\\', '_', $className).'_'.$append;
    }

    /**
     * get listing class.
     *
     * @return AbstractListing
     *
     * @throws Exception
     */
    public static function getList()
    {
        $listClass = get_called_class().'\\Listing';

        if (\Pimcore::getDiContainer()->has($listClass)) {
            return \Pimcore::getDiContainer()->make($listClass);
        }

        if (!Tool::classExists($listClass)) {
            throw new Exception("Listing Class $listClass not found!");
        }

        return new $listClass();
    }

    /**
     * Get all objects form this type.
     *
     * @return static[]
     */
    public static function getAll()
    {
        $list = self::getList();

        return $list->getData();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getCacheKey()
    {
        return static::getClassCacheKey(get_called_class(), $this->getId());
    }

    /**
     * save model to database.
     */
    public function save()
    {
        $this->getDao()->save();

        $cacheKey = self::getClassCacheKey(get_called_class(), $this->getId());

        //unset object in cache
        Cache::clearTag($cacheKey);
        Cache::remove($cacheKey);
        \Zend_Registry::set($cacheKey, null);
    }

    /**
     * delete model.
     *
     * @return bool
     */
    public function delete()
    {
        $cacheKey = self::getClassCacheKey(get_called_class(), $this->getId());

        //unset object in cache
        Cache::clearTag($cacheKey);
        \Zend_Registry::set($cacheKey, null);

        return $this->getDao()->delete();
    }

    /**
     * Get LocalizedFields Provider.
     *
     * @return LocalizedFields|null
     */
    public function getLocalizedFields()
    {
        if (count($this->localizedValues) > 0) {
            if (is_null($this->localizedFields)) {
                $this->localizedFields = new LocalizedFields($this->localizedValues);
                $this->localizedFields->setObject($this);
            }

            return $this->localizedFields;
        }

        return null;
    }

    /**
     * Get LocalizedFields Provider.
     *
     * @param $localizedFields
     */
    public function setLocalizedFields($localizedFields)
    {
        $this->localizedFields = $localizedFields;
    }

    /**
     * Override setValue function to support localized fields.
     *
     * @param $key
     * @param $value
     *
     * @return Model\AbstractModel
     */
    public function setValue($key, $value)
    {
        if ($this->getLocalizedFields()) {
            $mykey = explode('.', $key); //0 => key, 1 => language

            if (in_array($mykey [0], $this->localizedValues)) {
                $this->getLocalizedFields()->setLocalizedValue($mykey [0], $value, $mykey [1]);

                return $this;
            }
        }

        return parent::setValue($key, $value);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return strval($this->getId());
    }

    /**
     * Prepare object to goto sleep.
     *
     * @return array
     */
    public function __sleep()
    {
        $vars = parent::__sleep();
        $returnVars = [];
        $values = $this->getObjectVars();

        foreach ($vars as $key) {
            $value = $values[$key];

            if (!$value instanceof self) {
                $returnVars[] = $key;
            }
        }

        return $returnVars;
    }

    /**
     * Prepare object to wakeup.
     */
    public function __wakeup()
    {
        if ($this->getLocalizedFields()) {
            $this->getLocalizedFields()->setObject($this);
            $this->getLocalizedFields()->setFields($this->localizedValues);
        }
    }
}
