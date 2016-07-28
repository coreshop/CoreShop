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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model;

use CoreShop\Exception;
use CoreShop\Model\Listing\AbstractListing;
use Pimcore\Cache;
use Pimcore\File;
use Pimcore\Model;
use Pimcore\Tool;

/**
 * Class AbstractModel
 * @package CoreShop\Model
 */
class AbstractModel extends Model\AbstractModel
{
    /**
     * Array of all localized field names.
     *
     * @var array
     */
    protected $localizedValues = array();

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
        return self::getByShopId($id, null);
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
     * @param $id
     * @param null $shopId
     * @return mixed|null
     */
    public static function getByShopId($id, $shopId = null) {
        $id = intval($id);

        if ($id < 1) {
            return null;
        }

        $className = get_called_class();
        $cacheKey = self::getCacheKey($className, $id . ($shopId ? '_' . $shopId : ''));

        try {
            $object = \Zend_Registry::get($cacheKey);
            if (!$object) {
                throw new Exception($className.' in registry is null');
            }

            return $object;
        } catch (\Exception $e) {
            try {
                if (!$object = Cache::load($cacheKey)) {

                    if(\Pimcore::getDiContainer()->has($className)) {
                        $object = \Pimcore::getDiContainer()->make($className);
                    } else {
                        $object = new $className();
                    }

                    $object->getDao()->getById($id, $shopId);

                    \Zend_Registry::set($cacheKey, $object);
                    Cache::save($object, $cacheKey, array($cacheKey));
                } else {
                    \Zend_Registry::set($cacheKey, $object);
                }

                return $object;
            } catch (\Exception $e) {
                \Logger::warning($e->getMessage());
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
     * @return null|AbstractModel
     */
    public static function getByField($field, $value, $shopId = null)
    {
        $className = get_called_class();
        $cacheKey = self::getCacheKey($className, $field . '_' . File::getValidFilename(str_replace('-', '_', $value)) . ($shopId ? $shopId : ''));

        try {
            $object = \Zend_Registry::get($cacheKey);
            if (!$object) {
                throw new Exception($className.' in registry is null');
            }

            return $object;
        } catch (\Exception $e) {
            try {
                if (!$object = Cache::load($cacheKey)) {

                    if(\Pimcore::getDiContainer()->has($className)) {
                        $object = \Pimcore::getDiContainer()->make($className);
                    } else {
                        $object = new $className();
                    }

                    $object->getDao()->getByField($field, $value, $shopId);

                    \Zend_Registry::set($cacheKey, $object);
                    Cache::save($object, $cacheKey, array(self::getCacheKey(get_called_class(), $object->getId())));
                } else {
                    \Zend_Registry::set($cacheKey, $object);
                }

                return $object;
            } catch (\Exception $e) {
                \Logger::warning($e->getMessage());
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
    protected static function getCacheKey($className, $append)
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
     * save model to database.
     */
    public function save()
    {
        $this->getDao()->save();

        $cacheKey = self::getCacheKey(get_called_class(), $this->getId());

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
        $cacheKey = self::getCacheKey(get_called_class(), $this->getId());

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
     * @return $this
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
     * Prepare object to goto sleep.
     *
     * @return array
     */
    public function __sleep()
    {
        $vars = parent::__sleep();
        $returnVars = array();
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
