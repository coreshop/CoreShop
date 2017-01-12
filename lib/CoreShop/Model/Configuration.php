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
use CoreShop\Model\Configuration\Listing;
use CoreShop\Model\Index\Config;
use Pimcore\Logger;
use Pimcore\Tool;

/**
 * Class Configuration
 * @package CoreShop\Model
 */
class Configuration extends AbstractModel
{
    /**
     * @var bool
     */
    protected static $isMultiShopFK = true;

    /**
     * @var array
     */
    protected static $systemKeys = [
        'SYSTEM.ISINSTALLED',
        'SYSTEM.MULTISHOP.ENABLED',
        'SYSTEM.LOG.USAGESTATISTICS',
        'SYSTEM.CURRENCY.AUTO_EXCHANGE_RATES',
        'SYSTEM.CURRENCY.EXCHANGE_RATE_PROVIDER',
        'SYSTEM.CURRENCY.LAST_EXCHANGE_UPDATE',
        'SYSTEM.VISITORS.TRACK',
        'SYSTEM.VISITORS.KEEP_TRACKS_DAYS'
    ];

    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $data;

    /**
     * @var int
     */
    public $creationDate;

    /**
     * @var int
     */
    public $modificationDate;

    /**
     * @var int
     */
    public $shopId;

    /**
     * this is a small per request cache to know which configuration is which is, this info is used in self::getByKey().
     *
     * @var array
     */
    protected static $nameIdMappingCache = [];

    /**
     * Get By Id.
     *
     * @param int $id
     *
     * @return Configuration
     */
    public static function getById($id)
    {
        $cacheKey = 'coreshop_configuration_'.$id;

        try {
            $configurationEntry = \Zend_Registry::get($cacheKey);
            if (!$configurationEntry) {
                throw new Exception('Configuration in registry is null');
            }
        } catch (\Exception $e) {
            try {
                $configurationEntry = static::create();
                \Zend_Registry::set($cacheKey, $configurationEntry);
                $configurationEntry->setId(intval($id));
                $configurationEntry->getDao()->getById();
            } catch (\Exception $e) {
                Logger::error($e);

                return null;
            }
        }

        return $configurationEntry;
    }

    /**
     * Get by Key.
     *
     * @param string $key
     * @param int $shopId
     * @param bool   $returnObject
     *
     * @return mixed|null
     */
    public static function get($key, $shopId = null, $returnObject = false)
    {
        $cacheKey = $key . '~~~' . ($shopId ? $shopId : '-');

        if (Tool::isFrontend()) {
            if (!in_array($key, self::$systemKeys)) {
                if (self::multiShopEnabled()) {
                    if (is_null($shopId)) {
                        $shopId = Shop::getShop()->getId();
                    }
                }
            }
        }

        // check if pimcore already knows the id for this $name, if yes just return it
        if (array_key_exists($cacheKey, self::$nameIdMappingCache)) {
            $entry = self::getById(self::$nameIdMappingCache[$cacheKey]);

            if ($returnObject) {
                return $entry;
            }

            return $entry instanceof self ? $entry->getData() : null;
        }

        // create a tmp object to obtain the id
        $configurationEntry = static::create();

        try {
            $configurationEntry->getDao()->getByKey($key, $shopId);
        } catch (\Exception $e) {
            return null; //return silently.
        }

        // to have a singleton in a way. like all instances of Element\ElementInterface do also, like Object\AbstractObject
        if ($configurationEntry->getId() > 0) {
            // add it to the mini-per request cache
            self::$nameIdMappingCache[$cacheKey] = $configurationEntry->getId();
            $entry = self::getById($configurationEntry->getId());

            if ($returnObject) {
                return $entry;
            }

            return $entry instanceof self ? $entry->getData() : null;
        }
    }

    /**
     * set data for key.
     *
     * @param $key
     * @param $data
     * @param $shopId
     */
    public static function set($key, $data, $shopId = null)
    {
        $configEntry = self::get($key, $shopId, true);

        if (!$configEntry) {
            $configEntry = new self();
            $configEntry->setKey($key);
        }
        
        $configEntry->setShopId($shopId);
        $configEntry->setData($data);
        $configEntry->save();
    }

    /**
     * Remove all values from key
     *
     * @param $key
     */
    public static function remove($key)
    {
        $list = new Listing();
        $list->setFilter(function ($row) use ($key) {
            if ($row['key'] == $key) {
                return true;
            }

            return false;
        });

        $configurations = $list->getConfigurations();

        if (is_array($configurations)) {
            foreach ($configurations as $config) {
                $config->delete();
            }
        }
    }

    /**
     * get Plugin Config.
     *
     * @return mixed|null|\Zend_Config_Xml
     *
     * @throws \Zend_Exception
     */
    public static function getPluginConfig()
    {
        $config = null;

        if (\Zend_Registry::isRegistered('coreshop_plugin_config')) {
            $config = \Zend_Registry::get('coreshop_plugin_config');
        } else {
            try {
                $config = new \Zend_Config_Xml(CORESHOP_PLUGIN_CONFIG);
                self::setPluginConfig($config);
            } catch (\Exception $e) {
                if (is_file(CORESHOP_PLUGIN_CONFIG)) {
                    $m = 'Your plugin_xml.xml located at '.CORESHOP_PLUGIN_CONFIG.' is invalid, please check and correct it manually!';
                    Tool::exitWithError($m);
                }
            }
        }

        return $config;
    }

    /**
     * Set Plugin Config to \Zend_Registry.
     *
     * @static
     *
     * @param \Zend_Config $config
     */
    public static function setPluginConfig(\Zend_Config $config)
    {
        \Zend_Registry::set('coreshop_plugin_config', $config);
    }

    /**
     * Check if Catalog Mode is activated.
     *
     * @return bool
     */
    public static function isCatalogMode()
    {
        if (\Zend_Registry::isRegistered('coreshop_catalogmode')) {
            return \Zend_Registry::get('coreshop_catalogmode');
        } else {
            $catalogMode = intval(self::get('SYSTEM.BASE.CATALOGMODE')) === 1;

            if (is_bool($catalogMode)) {
                \Zend_Registry::set('coreshop_catalogmode', $catalogMode);

                return $catalogMode;
            }
        }

        return false;
    }

    /**
     * Check if guest checkout mode is activated.
     *
     * @return bool
     */
    public static function isGuestCheckoutActivated()
    {
        if (\Zend_Registry::isRegistered('coreshop_guestcheckout')) {
            return \Zend_Registry::get('coreshop_guestcheckout');
        } else {
            $guestCheckout = intval(self::get('SYSTEM.BASE.GUESTCHECKOUT')) === 1;

            if (is_bool($guestCheckout)) {
                \Zend_Registry::set('coreshop_guestcheckout', $guestCheckout);

                return $guestCheckout;
            }
        }

        return false;
    }

    /**
     * Check if multishop feature is activated
     *
     * @return bool|mixed
     * @throws \Zend_Exception
     */
    public static function multiShopEnabled()
    {
        if (\Zend_Registry::isRegistered('coreshop_multishop_enabled')) {
            return \Zend_Registry::get('coreshop_multishop_enabled');
        } else {
            $multiShop = intval(self::get('SYSTEM.MULTISHOP.ENABLED')) === 1;

            if (is_bool($multiShop)) {
                \Zend_Registry::set('coreshop_multishop_enabled', $multiShop);

                return $multiShop;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public static function getSystemKeys()
    {
        return self::$systemKeys;
    }

    /**
     * @param array $systemKeys
     */
    public static function setSystemKeys($systemKeys)
    {
        self::$systemKeys = $systemKeys;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("%s=%s (%s)", $this->getKey(), $this->getData(), $this->getId());
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return int
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * @param int $creationDate
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }

    /**
     * @return int
     */
    public function getModificationDate()
    {
        return $this->modificationDate;
    }

    /**
     * @param int $modificationDate
     */
    public function setModificationDate($modificationDate)
    {
        $this->modificationDate = $modificationDate;
    }

    /**
     * @return int
     */
    public function getShopId()
    {
        return $this->shopId;
    }

    /**
     * @param int $shopId
     */
    public function setShopId($shopId)
    {
        $this->shopId = $shopId;
    }
}
