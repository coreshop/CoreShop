<?php


class CoreShop_Plugin  extends Pimcore_API_Plugin_Abstract implements Pimcore_API_Plugin_Interface {

    /**
     * @var Zend_Translate
     */
    protected static $_translate;
    
    public function init() {

    }

    public static function install()
    {
        try 
        {
            $install = new CoreShop_Plugin_Install();
            // create object classes
            $categoryClass = $install->createClass('CoreShopCategory');
            $productClass = $install->createClass('CoreShopProduct');
            $cartClass = $install->createClass('CoreShopCart');
            
            // create root object folder with subfolders
            $coreShopFolder = $install->createFolders();
            // create custom view for blog objects
            $install->createCustomView($coreShopFolder, array(
                $productClass->getId(),
                $categoryClass->getId(),
                $cartClass->getId()
            ));
            // create static routes
            $install->createStaticRoutes();
            // create predefined document types
            //$install->createDocTypes();
        } 
        catch(Exception $e) 
        {
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
            $install = new CoreShop_Plugin_Install();
            
            // remove predefined document types
            //$install->removeDocTypes();
            // remove static routes
            $install->removeStaticRoutes();
            
            // remove custom view
            $install->removeCustomView();
            // remove object folder with all childs
            
            $install->removeFolders();
            // remove classes
            
            $install->removeClass('CoreShopProduct');
            $install->removeClass('CoreShopCategory');
            $install->removeClass('CoreShopCart');
            
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
        $entry = Object_Class::getByName('CoreShopProduct');
        $category = Object_Class::getByName('CoreShopProduct');
        $cart = Object_Class::getByName('CoreShopCart');
        
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
        if(self::$_translate instanceof Zend_Translate) {
            return self::$_translate;
        }
        try {
            $lang = Zend_Registry::get('Zend_Locale')->getLanguage();
        } catch (Exception $e) {
            $lang = 'en';
        }
        self::$_translate = new Zend_Translate(
            'csv',
            PIMCORE_PLUGINS_PATH . self::getTranslationFile($lang),
            $lang,
            array('delimiter' => ',')
        );
        return self::$_translate;
    }
}
