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
 * @copyright  Copyright (c) Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Configuration\Model;

use CoreShop\Component\Core\Model\AbstractResource;
use Pimcore\Logger;
use Pimcore\Tool;

class Configuration extends AbstractResource implements ConfigurationInterface
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
     * @return static
     */
    public static function getById($id)
    {
        $cacheKey = 'coreshop_configuration_'.$id;

        try {
            $configurationEntry = \Zend_Registry::get($cacheKey);
            if (!$configurationEntry) {
                throw new \Exception('Configuration in registry is null');
            }
        } catch (\Exception $e) {
            try {
                $configurationEntry = new static();
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
     * @return static
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
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
     * @return static
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
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
     * @return static
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
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
     * @return static
     */
    public function setModificationDate($modificationDate)
    {
        $this->modificationDate = $modificationDate;

        return $this;
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
     * @return static
     */
    public function setShopId($shopId)
    {
        $this->shopId = $shopId;

        return $this;
    }
}