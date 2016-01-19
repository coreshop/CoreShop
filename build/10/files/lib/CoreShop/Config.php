<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop;

use Pimcore\Tool;

class Config {

    /**
     * @static
     * @return \Zend_Config
     */
    public static function getConfig () {

        $config = null;

        if(\Zend_Registry::isRegistered("coreshop_config")) {
            $config = \Zend_Registry::get("coreshop_config");
        } else  {
            try {
                $config = new \Zend_Config_Xml(CORESHOP_CONFIGURATION);
                self::setConfig($config);
            } catch (\Exception $e) {
                \Logger::emergency("Cannot find system configuration, should be located at: " . CORESHOP_CONFIGURATION);
                if(is_file(CORESHOP_CONFIGURATION)) {
                    $m = "Your coreshop-config.xml located at " . CORESHOP_CONFIGURATION . " is invalid, please check and correct it manually!";
                    Tool::exitWithError($m);
                }
            }
        }

        return $config;
    }

    /**
     * @return mixed|null|\Zend_Config_Xml
     * @throws \Zend_Exception
     */
    public static function getPluginConfig()
    {
        $config = null;

        if(\Zend_Registry::isRegistered("coreshop_plugin_config")) {
            $config = \Zend_Registry::get("coreshop_plugin_config");
        } else  {
            try {
                $config = new \Zend_Config_Xml(CORESHOP_PLUGIN_CONFIG);
                self::setPluginConfig($config);
            } catch (\Exception $e) {
                if(is_file(CORESHOP_PLUGIN_CONFIG)) {
                    $m = "Your plugin_xml.xml located at " . CORESHOP_PLUGIN_CONFIG . " is invalid, please check and correct it manually!";
                    Tool::exitWithError($m);
                }
            }
        }

        return $config;
    }

    /**
     * @static
     * @param \Zend_Config $config
     * @return void
     */
    public static function setPluginConfig (\Zend_Config $config) {
        \Zend_Registry::set("coreshop_plugin_config", $config);
    }

    /**
     * Gets a value in config by a key-path eg currency.default
     *
     * @param $key
     * @return mixed
     */
    public static function getValue($key) {
        $config = self::getConfig()->toArray();
        $pathParts = explode('.', strtolower($key));

        $current = &$config;
        foreach($pathParts as $key) {
            $current = &$current[$key];
        }

        return $current;
    }

    /**
     * Sets a value in config by a key-path eg. currency.default
     *
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function setValue($key, $value) {
        $config = self::getConfig()->toArray();
        $pathParts = explode('.', strtolower($key));

        $current = &$config;
        foreach($pathParts as $key) {
            $current = &$current[$key];
        }

        $current = $value;

        $config = new \Zend_Config($config, true);
        $writer = new \Zend_Config_Writer_Xml(array(
            "config" => $config,
            "filename" => CORESHOP_CONFIGURATION
        ));
        $writer->write();

        return $current;
    }

    /**
     * @static
     * @param \Zend_Config $config
     * @return void
     */
    public static function setConfig (\Zend_Config $config) {
        \Zend_Registry::set("coreshop_config", $config);
    }

    /**
     * Check if Catalog Mode is activated
     *
     * @return bool
     */
    public static function isCatalogMode() {
        if(\Zend_Registry::isRegistered("coreshop_catalogmode")) {
            return \Zend_Registry::get("coreshop_catalogmode");
        }
        else {
            $catalogMode = intval(self::getValue("base.catalog-mode")) === 1;

            if(is_bool($catalogMode)) {
                \Zend_Registry::set("coreshop_catalogmode", $catalogMode);

                return $catalogMode;
            }
        }

        return false;
    }

    /**
     * Check if guest checkout mode is activated
     *
     * @return bool
     */
    public static function isGuestCheckoutActivated() {
        if(\Zend_Registry::isRegistered("coreshop_guestcheckout")) {
            return \Zend_Registry::get("coreshop_guestcheckout");
        }
        else {
            $guestCheckout = intval(self::getValue("base.guest-checkout")) === 1;

            if(is_bool($guestCheckout)) {
                \Zend_Registry::set("coreshop_guestcheckout", $guestCheckout);

                return $guestCheckout;
            }
        }

        return false;
    }
}
