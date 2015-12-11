<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */

namespace CoreShop;

use Pimcore\API\Plugin\AbstractPlugin;
use Pimcore\API\Plugin\PluginInterface;
use Pimcore\Model\Object;

use CoreShop\Model\Plugin\Shipping;
use CoreShop\Model\Plugin\Payment;
use CoreShop\Model\Plugin\Hook;
use CoreShop\Model\Plugin\InstallPlugin;

use CoreShop\Plugin\Install;

class Plugin extends AbstractPlugin implements PluginInterface {

    /**
     * @var \Zend_Translate
     */
    protected static $_translate;

    public function __construct($jsPaths = null, $cssPaths = null) {
        require_once(PIMCORE_PLUGINS_PATH . "/CoreShop/config/startup.php");
        require_once(PIMCORE_PLUGINS_PATH . "/CoreShop/config/helper.php");

        parent::__construct($jsPaths, $cssPaths);

    }

    public static function installPlugin(InstallPlugin $installPlugin)
    {
        $install = new Install();
        $installPlugin->install($install);
    }

    public static function uninstallPlugin(InstallPlugin $installPlugin)
    {
        $install = new Install();
        $installPlugin->uninstall($install);
    }

    public static function install()
    {
        try
        {
            $install = new Install();

            $install->createConfig();

            self::getEventManager()->trigger('install.post', null, array("installer" => $install));
        }
        catch(Exception $e)
        {
            \Logger::crit($e);
            return self::getTranslate()->_('coreshop_install_failed');
        }

        return self::getTranslate()->_('coreshop_installed_successfully');
    }

    /**
     * @return string $statusMessage
     */
    public static function uninstall()
    {
        try {
            $install = new Install();

            self::getEventManager()->trigger('uninstall.pre', null, array("installer" => $install));

            // remove predefined document types
            //$install->removeDocTypes();
            // remove static routes
            $install->removeStaticRoutes();

            // remove custom view
            $install->removeCustomView();
            // remove object folder with all childs

            $install->removeFolders();
            // remove classes

            $install->removeClassmap();

            //$install->removeClass("CoreShopCartRule");
            $install->removeClass('CoreShopProduct');
            $install->removeClass('CoreShopCategory');
            $install->removeClass('CoreShopCart');
            $install->removeClass('CoreShopCartItem');
            $install->removeClass("CoreShopUser");
            $install->removeClass("CoreShopOrder");
            $install->removeClass("CoreShopOrderState");
            $install->removeClass("CoreShopPayment");
            $install->removeClass("CoreShopOrderItem");

            $install->removeFieldcollection('CoreShopUserAddress');
            $install->removeImageThumbnails();
            $install->removeConfig();

            self::getEventManager()->trigger('uninstall.post', null, array("installer" => $install));

            return self::getTranslate()->_('coreshop_uninstalled_successfully');
        } catch (Exception $e) {
            \Logger::crit($e);
            return self::getTranslate()->_('coreshop_uninstall_failed');
        }
    }

    public static function coreShopIsInstalled()
    {
        $entry = Object\ClassDefinition::getByName('CoreShopProduct');
        $category = Object\ClassDefinition::getByName('CoreShopProduct');
        $cartItem = Object\ClassDefinition::getByName('CoreShopCart');
        $cart = Object\ClassDefinition::getByName('CoreShopCartItem');
        $order = Object\ClassDefinition::getByName('CoreShopOrder');
        $orderItem = Object\ClassDefinition::getByName('CoreShopOrderItem');
        $orderPayment = Object\ClassDefinition::getByName('CoreShopPayment');
        $orderState = Object\ClassDefinition::getByName('CoreShopOrderState');

        if ($entry && $category && $cart && $cartItem && $order && $orderItem && $orderPayment && $orderState) {
            return true;
        }

        return false;
    }

    /**
     * @return boolean $isInstalled
     */
    public static function isInstalled()
    {
        $config = Config::getConfig();

        if($config) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public static function getTranslationFileDirectory()
    {
        return PIMCORE_PLUGINS_PATH . '/CoreShop/static/texts';
    }

    /**
     * @param string $language
     * @return string path to the translation file relative to plugin directory
     */
    public static function getTranslationFile($language)
    {
        if (is_file(self::getTranslationFileDirectory() . "/$language.csv")) {
            return "/CoreShop/static/texts/$language.csv";
        } else {
            return '/CoreShop/static/texts/en.csv';
        }
    }


    /**
     * @return Zend_Translate
     */
    public static function getTranslate()
    {
        if(self::$_translate instanceof \Zend_Translate) {
            return self::$_translate;
        }
        try {
            $lang = \Zend_Registry::get('Zend_Locale')->getLanguage();
        } catch (Exception $e) {
            $lang = 'en';
        }
        self::$_translate = new \Zend_Translate(
            'csv',
            PIMCORE_PLUGINS_PATH . self::getTranslationFile($lang),
            $lang,
            array('delimiter' => ',')
        );
        return self::$_translate;
    }


    public static function getClassmapFile()
    {
        return PIMCORE_CONFIGURATION_DIRECTORY . "/coreshop_classmap.xml";
    }

    //*************


    /**
     * @var Zend_EventManager_EventManager
     */
    private static $eventManager;

    private static $layout = "shop";

    /**
     * @return Zend_EventManager_EventManager
     */
    public static function getEventManager() {
        if(!self::$eventManager) {
            self::$eventManager = new \Zend_EventManager_EventManager();
        }
        return self::$eventManager;
    }

    public static function getLayout() {
        return self::$layout;
    }

    public static function setLayout($layout) {
        self::$layout = $layout;
    }

    public static function getShippingProviders(Object\CoreShopCart $cart)
    {
        $results = self::getEventManager()->trigger("shipping.getProvider", null, array("cart" => $cart), function($v) {
            return ($v instanceof Shipping);
        });

        if($results->stopped())
        {
            $provider = array();

            foreach($results as $result)
            {
                $provider[] = $result;
            }

            return $provider;
        }

        return array();
    }

    public static function getShippingProvider($identifier)
    {
        $results = self::getEventManager()->trigger("shipping.getProvider", null, array(), function($v) use ($identifier) {
            return ($v instanceof Shipping && $v->getIdentifier() == $identifier);
        });

        if($results->stopped())
        {
            return $results->last();
        }

        return false;
    }

    public static function getPaymentProviders(Object\CoreShopCart $cart)
    {
        $results = self::getEventManager()->trigger("payment.getProvider", null, array("cart" => $cart), function($v) {
            return ($v instanceof Payment);
        });

        if($results->stopped())
        {
            $provider = array();

            foreach($results as $result)
            {
                $provider[] = $result;
            }

            return $provider;
        }

        return array();
    }

    public static function getPaymentProvider($identifier)
    {
        $providers = self::getPaymentProviders(null);

        foreach($providers as $provider)
        {
            if($provider->getIdentifier() == $identifier)
                return $provider;
        }

        return false;
    }

    public static function hook($name, $params = array())
    {
        $results = self::getEventManager()->trigger("hook." . $name, null, array(), function($v) {
            return ($v instanceof Hook);
        });

        $params['language'] = \Zend_Registry::get("Zend_Locale");

        if($results->stopped())
        {
            $return = array();

            foreach($results as $result)
            {
                $return[] = $result->render($params);
            }

            return implode($return, "\n");
        }

        return "";
    }

    public static function actionHook($name, $params = array())
    {
        $results = self::getEventManager()->trigger("actionHook." . $name, null, array(), function($v) {
            return ($v instanceof Hook);
        });

        $params['language'] = \Zend_Registry::get("Zend_Locale");

        if($results->stopped())
        {
            $return = array();

            foreach($results as $result)
            {
                $return[] = $result->render($params);
            }

            return implode($return, "\n");
        }

        return "";
    }
}
