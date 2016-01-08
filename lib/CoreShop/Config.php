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
     * @return \Zend_Config_Xml
     */
    public static function getModelClassMappingConfig () {

        $config = null;

        if(\Zend_Registry::isRegistered("coreshop_config_model_classmapping")) {
            $config = \Zend_Registry::get("coreshop_config_model_classmapping");
        } else {
            $mappingFile = PIMCORE_CONFIGURATION_DIRECTORY . "/coreshop_classmap.xml";

            if(is_file($mappingFile) && is_readable($mappingFile)) {
                try {
                    $config = new \Zend_Config_Xml($mappingFile);
                    self::setModelClassMappingConfig($config);
                } catch (Exception $e) {
                    \Logger::error("coreshop_classmap.xml exists but it is not a valid Zend_Config_Xml configuration. Maybe there is a syntaxerror in the XML.");
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
    public static function setModelClassMappingConfig (\Zend_Config $config) {
        \Zend_Registry::set("coreshop_config_model_classmapping", $config);
    }

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
}
