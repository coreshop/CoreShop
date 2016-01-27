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

use CoreShop\Exception\ThemeNotFoundException;
use CoreShop\Model\Cart;
use CoreShop\Model\Configuration;
use CoreShop\Model\Zone;
use Pimcore\API\Plugin\AbstractPlugin;
use Pimcore\API\Plugin\PluginInterface;
use Pimcore\Model\Object;

use CoreShop\Model\Plugin\Shipping;
use CoreShop\Model\Plugin\Payment;
use CoreShop\Model\Plugin\Hook;
use CoreShop\Model\Plugin\InstallPlugin;

use CoreShop\Plugin\Install;

use CoreShopTemplate\Theme as TemplateTheme;

class Plugin extends AbstractPlugin implements PluginInterface {

    /**
     * @var \Zend_Translate
     */
    protected static $_translate;

    /**
     * @var \CoreShop\Theme
     */
    protected static $_theme;

    /**
     * Plugin constructor.
     * @param null $jsPaths
     * @param null $cssPaths
     */
    public function __construct($jsPaths = null, $cssPaths = null) {
        require_once(PIMCORE_PLUGINS_PATH . "/CoreShop/config/startup.php");
        require_once(PIMCORE_PLUGINS_PATH . "/CoreShop/config/helper.php");

        parent::__construct($jsPaths, $cssPaths);

    }

    public function init()
    {
        \Pimcore::getEventManager()->attach('system.console.init', function(\Zend_EventManager_Event $e) {
            /** @var \Pimcore\Console\Application $application */
            $application = $e->getTarget();

            // add a namespace to autoload commands from
            $application->addAutoloadNamespace('CoreShop\\Console', CORESHOP_PATH . '/lib/CoreShop/Console');

            // add a single command
            $application->add(new \CoreShop\Console\Command\UpdateCommand());
        });

        \Pimcore::getEventManager()->attach("system.startup", function (\Zend_EventManager_Event $e) {
            $autoloader = \Zend_Loader_Autoloader::getInstance();
            $frontController = $e->getTarget();

            $router = $frontController->getRouter();

            $routePluginPayment = new \Zend_Controller_Router_Route(
                '/:lang/shop/payment/:action/*',
                array(
                    "controller" => "payment",
                    "action" => "index"
                )
            );

            $router->addRoute("coreshop_payment", $routePluginPayment);

            if($frontController instanceof \Zend_Controller_Front) {
                $namespace = "CoreShopTemplate";

                $frontController->addControllerDirectory(CORESHOP_TEMPLATE_PATH . "/controllers", $namespace);

                $autoloader->registerNamespace($namespace);

                $includePaths = array(
                    get_include_path(),
                    CORESHOP_TEMPLATE_PATH . "/controllers",
                    CORESHOP_TEMPLATE_PATH . "/lib"
                );
                set_include_path(implode(PATH_SEPARATOR, $includePaths) . PATH_SEPARATOR);

                $frontController->registerPlugin(new Controller\Plugin\TemplateRouter());
            }
        });
    }

    /**
     * Install Plugin
     *
     * @param InstallPlugin $installPlugin
     */
    public static function installPlugin(InstallPlugin $installPlugin)
    {
        $install = new Install();
        $installPlugin->install($install);
    }

    /**
     * Uninstall Plugin
     *
     * @param InstallPlugin $installPlugin
     */
    public static function uninstallPlugin(InstallPlugin $installPlugin)
    {
        $install = new Install();
        $installPlugin->uninstall($install);
    }

    /**
     * Install Pimcore CoreShop Plugin
     *
     * @return mixed
     */
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
            $install->removeConfig();

            self::getEventManager()->trigger('uninstall.post', null, array("installer" => $install));

            return self::getTranslate()->_('coreshop_uninstalled_successfully');
        } catch (Exception $e) {
            \Logger::crit($e);
            return self::getTranslate()->_('coreshop_uninstall_failed');
        }
    }

    /**
     * Check if CoreShop is installed
     *
     * @return bool
     */
    public static function coreShopIsInstalled()
    {
        $entry = Object\ClassDefinition::getByName('CoreShopProduct');
        $category = Object\ClassDefinition::getByName('CoreShopProduct');
        $cartItem = Object\ClassDefinition::getByName('CoreShopCart');
        $cart = Object\ClassDefinition::getByName('CoreShopCartItem');
        $order = Object\ClassDefinition::getByName('CoreShopOrder');
        $orderItem = Object\ClassDefinition::getByName('CoreShopOrderItem');
        $orderPayment = Object\ClassDefinition::getByName('CoreShopPayment');

        if ($entry && $category && $cart && $cartItem && $order && $orderItem && $orderPayment) {
            return true;
        }

        return false;
    }

    /**
     * @return boolean $isInstalled
     */
    public static function isInstalled()
    {
        $config = Configuration::get("SYSTEM.ISINSTALLED");

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
     * @return \Zend_Translate
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

    /**
     * Enables a theme
     *
     * @param $name
     * @throws ThemeNotFoundException
     */
    public static function enableTheme($name)
    {
        if($themeDir = self::getThemeDirectory($name))
        {
            //disable current template
            $currentTemplate = Configuration::get("SYSTEM.TEMPLATE.NAME");

            if($oldThemeDir = self::getThemeDirectory($currentTemplate)) {
                $disableScript = $themeDir . "/disable.php";

                if(is_file($disableScript)) {
                    include($disableScript);
                }
            }

            //enable new template
            $enableScript = $themeDir . "/enable.php";

            if(is_file($enableScript)) {
                include($enableScript);
            }

            self::getTheme()->installTheme();
        }
        else {
            throw new ThemeNotFoundException();
        }
    }

    /**
     * Returns theme directory
     *
     * @param $name
     * @return bool|string
     */
    public static function getThemeDirectory($name) {
        if (is_dir(PIMCORE_WEBSITE_PATH . '/views/coreshop/template/' . $name)) {
            return PIMCORE_WEBSITE_PATH . '/views/coreshop/template/' . $name;

        }
        else if(is_dir(CORESHOP_PATH . "/views/template/" . $name))
        {
            return CORESHOP_PATH . "/views/template/" . $name;
        }

        return false;
    }

    /**
     * @return Theme|\CoreShopTemplate\Theme
     */
    public static function getTheme() {
        if(!self::$_theme instanceof Theme) {
            self::$_theme = new TemplateTheme();
        }

        return self::$_theme;
    }

    /**
     * @var \Zend_EventManager_EventManager
     */
    private static $eventManager;

    private static $layout = "shop";

    /**
     * @return \Zend_EventManager_EventManager
     */
    public static function getEventManager() {
        if(!self::$eventManager) {
            self::$eventManager = new \Zend_EventManager_EventManager();
        }
        return self::$eventManager;
    }

    /**
     * Get CoreShop default layout
     *
     * @return string
     */
    public static function getLayout() {
        return self::$layout;
    }

    /**
     * Set CoreShop default layout
     *
     * @param $layout
     */
    public static function setLayout($layout) {
        self::$layout = $layout;
    }

    /**
     * Get PaymentProviders
     *
     * @param Cart $cart
     * @return array
     */
    public static function getPaymentProviders(Cart $cart = null)
    {
        $results = self::getEventManager()->trigger("payment.getProvider");
        $provider = array();

        foreach($results as $result)
        {
            if($result instanceof Payment) {
                if($cart instanceof Cart) {
                    if($result->isAvailable($cart)) {
                        $provider[] = $result;
                    }
                }
                else {
                    $provider[] = $result;
                }
            }
        }

        return $provider;
    }

    /**
     * Get PaymentProvider by identifier
     *
     * @param $identifier
     * @return bool
     */
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

    /**
     * Call a CoreShop hook
     *
     * @param $name
     * @param array $params
     * @return string
     * @throws \Zend_Exception
     */
    public static function hook($name, $params = array())
    {
        $results = self::getEventManager()->trigger("hook." . $name, null, array());

        $params['language'] = \Zend_Registry::get("Zend_Locale");

        if(count($results) > 0) {
            $return = array();

            foreach ($results as $result) {
                $return[] = $result->render($params);
            }

            return implode($return, "\n");
        }

        return false;
    }

    /**
     * Call an action hook
     *
     * @param $name
     * @param array $params
     * @return string
     * @throws \Zend_Exception
     */
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
