<?php

namespace CoreShop;

use Pimcore\API\Plugin\AbstractPlugin;
use Pimcore\API\Plugin\PluginInterface;
use Pimcore\Model\Object;

use CoreShop\Plugin\Install;

class Plugin extends AbstractPlugin implements PluginInterface {

    /**
     * @var Zend_Translate
     */
    protected static $_translate;
    
    public function init() {
        require_once(PIMCORE_PLUGINS_PATH . "/CoreShop/config/helper.php");
    }

    public static function install()
    {
        try 
        {
            $install = new Install();
            
            self::getEventManager()->trigger('install.pre', $this, array("installer" => $install));
            
            // create object classes
            $categoryClass = $install->createClass('CoreShopCategory');
            $productClass = $install->createClass('CoreShopProduct');
            $cartClass = $install->createClass('CoreShopCart');
            $cartItemClass = $install->createClass('CoreShopCartItem');
            $userClass = $install->createClass("CoreShopUser");
            
            $orderItemClass = $install->createClass("CoreShopOrderItem");
            $paymentClass = $install->createClass("CoreShopPayment");
            $orderClass = $install->createClass("CoreShopOrder");
            
            $fcUserAddress = $install->createFieldcollection('CoreShopUserAddress');
            
            // create root object folder with subfolders
            $coreShopFolder = $install->createFolders();
            // create custom view for blog objects
            $install->createCustomView($coreShopFolder, array(
                $productClass->getId(),
                $categoryClass->getId(),
                $cartClass->getId(),
                $cartItemClass->getId(),
                $userClass->getId(),
                $orderItemClass->getId(),
                $orderClass->getId(),
                $paymentClass->getId()
            ));
            // create static routes
            $install->createStaticRoutes();
            // create predefined document types
            //$install->createDocTypes();
            
            $install->createClassmap();
            $install->createImageThumbnails();
            
            self::getEventManager()->trigger('install.post', $this, array("installer" => $install));
        } 
        catch(Exception $e) 
        {
            throw $e;
            logger::crit($e);
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
            
            self::getEventManager()->trigger('uninstall.pre', $this, array("installer" => $install));
            
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
            
            $install->removeClass('CoreShopProduct');
            $install->removeClass('CoreShopCategory');
            $install->removeClass('CoreShopCart');
            $install->removeClass('CoreShopCartItem');
            $install->removeClass("CoreShopUser");
            $install->removeClass("CoreShopOrder");
            $install->removeClass("CoreShopPayment");
            $install->removeClass("CoreShopOrderItem");
            
            $install->removeFieldcollection('CoreShopUserAddress');
            $install->removeImageThumbnails();
            
            self::getEventManager()->trigger('uninstall.post', $this, array("installer" => $install));
            
            return self::getTranslate()->_('coreshop_uninstalled_successfully');
        } catch (Exception $e) {
            Logger::crit($e);
            return self::getTranslate()->_('coreshop_uninstall_failed');
        }
    }
    /**
     * @return boolean $isInstalled
     */
    public static function isInstalled()
    {
        $entry = Object\ClassDefinition::getByName('CoreShopProduct');
        $category = Object\ClassDefinition::getByName('CoreShopProduct');
        $cart = Object\ClassDefinition::getByName('CoreShopCart');
        $cart = Object\ClassDefinition::getByName('CoreShopCartItem');
        $order = Object\ClassDefinition::getByName('CoreShopOrder');
        $orderItem = Object\ClassDefinition::getByName('CoreShopOrderItem');
        $orderPayment = Object\ClassDefinition::getByName('CoreShopPayment');
        
        if ($entry && $category && $cart) {
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
    
    public static function getDeliveryProviders(Object\CoreShopCart $cart)
    {
        $results = self::getEventManager()->trigger("delivery.getProvider", null, array("cart" => $cart), function($v) {
            return ($v instanceof \CoreShop\Plugin\Delivery);
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
    
    public static function getDeliveryProvider($identifier)
    {
        $results = self::getEventManager()->trigger("delivery.getProvider", null, array("cart" => $cart), function($v) {
            return ($v instanceof \CoreShop\Plugin\Delivery && $v->getIdentifier() == $identifier);
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
            return ($v instanceof \CoreShop\Plugin\Payment);
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
    
    public static function hook($name, $params)
    {
        $results = self::getEventManager()->trigger("hook." . $name, null, array(), function($v) {
            return ($v instanceof \CoreShop\Plugin\Hook);
        });
        

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
