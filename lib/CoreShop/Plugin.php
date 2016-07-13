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

namespace CoreShop;

use CoreShop\Exception\ThemeNotFoundException;
use CoreShop\Model\Cart;
use CoreShop\Model\Configuration;
use CoreShop\Model\Product;
use CoreShop\Model\TaxRule\VatManager;
use DI\ContainerBuilder;
use Pimcore\API\Plugin\AbstractPlugin;
use Pimcore\API\Plugin\PluginInterface;
use Pimcore\Cache;
use Pimcore\Model\Object;
use CoreShop\Model\Plugin\Payment;
use CoreShop\Model\Plugin\Hook;
use CoreShop\Model\Plugin\InstallPlugin;
use CoreShop\Plugin\Install;
use Pimcore\Model\Schedule\Maintenance\Job;
use Pimcore\Model\Schedule\Manager\Procedural;

/**
 * Class Plugin
 * @package CoreShop
 */
class Plugin extends AbstractPlugin implements PluginInterface
{
    /**
     * @var \Zend_Translate
     */
    protected static $_translate;

    /**
     * Plugin constructor.
     *
     * @param null $jsPaths
     * @param null $cssPaths
     */
    public function __construct($jsPaths = null, $cssPaths = null)
    {
        parent::__construct($jsPaths, $cssPaths);
    }

    /**
     * Init Plugin.
     *
     * @throws \Zend_EventManager_Exception_InvalidArgumentException
     */
    public function init()
    {
        \Pimcore::getEventManager()->attach('system.console.init', function (\Zend_EventManager_Event $e) {
            /** @var \Pimcore\Console\Application $application */
            $application = $e->getTarget();

            // add a namespace to autoload commands from
            $application->addAutoloadNamespace('CoreShop\\Console', CORESHOP_PATH.'/lib/CoreShop/Console');
        });

        \Pimcore::getEventManager()->attach('system.startup', function (\Zend_EventManager_Event $e) {
            $frontController = $e->getTarget();

            if ($frontController instanceof \Zend_Controller_Front) {
                $frontController->registerPlugin(new Controller\Plugin\TemplateRouter());
                $frontController->registerPlugin(new Controller\Plugin\Debug());
            }
        });

        \Pimcore::getEventManager()->attach('system.console.init', function (\Zend_EventManager_Event $e) {

            $autoloader = \Zend_Loader_Autoloader::getInstance();
            $autoloader->registerNamespace('CoreShopTemplate');

            $includePaths = array(
                get_include_path(),
                CORESHOP_TEMPLATE_PATH.'/controllers',
                CORESHOP_TEMPLATE_PATH.'/lib',
            );
            set_include_path(implode(PATH_SEPARATOR, $includePaths).PATH_SEPARATOR);

        });

        \Pimcore::getEventManager()->attach('system.maintenance', function (\Zend_EventManager_Event $e) {
            $manager = $e->getTarget();

            if ($manager instanceof Procedural) {
                if (Configuration::get('SYSTEM.CURRENCY.AUTO_EXCHANGE_RATES')) {
                    $manager->registerJob(new Job('coreshop_exchangerates', '\\CoreShop\\Model\\Currency\\ExchangeRates', 'maintenance'));
                }
                if (Configuration::get('SYSTEM.CART.AUTO_CLEANUP')) {
                    $manager->registerJob(new Job('coreshop_cart_cleanup', '\\CoreShop\\Model\\Cart', 'maintenance'));
                }
            }
        });

        \Pimcore::getEventManager()->attach('object.postAdd', array($this, 'postAddObject'));
        \Pimcore::getEventManager()->attach('object.postAdd', array($this, 'postAddObject'));
        \Pimcore::getEventManager()->attach('object.postUpdate', array($this, 'postUpdateObject'));
        \Pimcore::getEventManager()->attach('object.postDelete', array($this, 'postDeleteObject'));

        \Pimcore::getEventManager()->attach("system.di.init", function (\Zend_EventManager_Event $e) {
            $diBuilder = $e->getTarget();
            
            if ($diBuilder instanceof ContainerBuilder) {
                $diBuilder->addDefinitions(CORESHOP_PATH . "/config/di.php");
            }
        });

        //Allows to load classes with CoreShop namespace from Website (eg. for overriding classes)
        $includePaths = array(
            get_include_path(),
            PIMCORE_WEBSITE_PATH.'/lib/CoreShop',
        );
        set_include_path(implode(PATH_SEPARATOR, $includePaths));

        $this->startup();

        if(Configuration::multiShopEnabled()) {
            Product\PriceRule::addCondition('shop');
            Cart\PriceRule::addCondition('shop');
        }

        if (Configuration::get('SYSTEM.BASE.DISABLEVATFORBASECOUNTRY')) {
            \Pimcore::getEventManager()->attach('coreshop.tax.getTaxManager', function () {
                return new VatManager();
            });
        }
    }

    /**
     * Startup CoreShop
     *
     * This method initializes the defined Constants for pathes and loads the template paths
     */
    protected function startup() {
        require_once PIMCORE_PLUGINS_PATH.'/CoreShop/config/helper.php';

        if (!defined("CORESHOP_PATH")) {
            define("CORESHOP_PATH", PIMCORE_PLUGINS_PATH . "/CoreShop");
        }
        if (!defined("CORESHOP_PLUGIN_CONFIG")) {
            define("CORESHOP_PLUGIN_CONFIG", CORESHOP_PATH . "/plugin.xml");
        }
        if (!defined("CORESHOP_CONFIGURATION_PATH")) {
            define("CORESHOP_CONFIGURATION_PATH", PIMCORE_CONFIGURATION_DIRECTORY);
        }
        if (!defined("CORESHOP_TEMPORARY_DIRECTORY")) {
            define("CORESHOP_TEMPORARY_DIRECTORY", PIMCORE_TEMPORARY_DIRECTORY);
        }
        if (!defined("CORESHOP_UPDATE_DIRECTORY")) {
            define("CORESHOP_UPDATE_DIRECTORY", CORESHOP_PATH . "/update");
        }

        if (!defined("CORESHOP_BUILD_DIRECTORY")) {
            define("CORESHOP_BUILD_DIRECTORY", CORESHOP_PATH . "/build");
        }
    }

    /**
     * Post Update Object.
     *
     * @param \Zend_EventManager_Event $e
     */
    public function postUpdateObject(\Zend_EventManager_Event $e)
    {
        $object = $e->getTarget();
        if ($object instanceof Product) {
            $indexService = IndexService::getIndexService();
            $indexService->updateIndex($object);

            $object->clearPriceCache();
        }
    }

    /**
     * Pre Delete Object.
     *
     * @param \Zend_EventManager_Event $e
     */
    public function preDeleteObject(\Zend_EventManager_Event $e)
    {
        $object = $e->getTarget();
        if ($object instanceof Product) {
            $indexService = IndexService::getIndexService();
            $indexService->deleteFromIndex($object);
        }
    }

    /**
     * Post Delete Object
     *
     * @param \Zend_EventManager_Event $e
     */
    public function postDeleteObject(\Zend_EventManager_Event $e) {
        $object = $e->getTarget();
        if ($object instanceof Product) {
            $prices = Product\SpecificPrice::getSpecificPrices($object);

            foreach ($prices as $pr) {
                $pr->delete();
            }
        }
    }

    /**
     * Install Plugin.
     *
     * @param InstallPlugin $installPlugin
     */
    public static function installPlugin(InstallPlugin $installPlugin)
    {
        $install = new Install();
        $installPlugin->install($install);
    }

    /**
     * Uninstall Plugin.
     *
     * @param InstallPlugin $installPlugin
     */
    public static function uninstallPlugin(InstallPlugin $installPlugin)
    {
        $install = new Install();
        $installPlugin->uninstall($install);
    }

    /**
     * Install Pimcore CoreShop Plugin.
     *
     * @return mixed
     */
    public static function install()
    {
        try {
            $install = new Install();

            $install->executeSQL('CoreShop');
            $install->executeSQL('CoreShop-States');
            $install->createConfig();

            \Pimcore::getEventManager()->trigger('coreshop.install.post', null, array('installer' => $install));
        } catch (Exception $e) {
            \Logger::crit($e);

            return self::getTranslate()->_('coreshop_install_failed');
        }

        return self::getTranslate()->_('coreshop_installed_successfully');
    }

    /**
     * Uninstall CoreShop.
     *
     * @return string $statusMessage
     */
    public static function uninstall()
    {
        try {
            $install = new Install();

            \Pimcore::getEventManager()->trigger('coreshop.uninstall.pre', null, array('installer' => $install));

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
            $install->removeClass('CoreShopUser');
            $install->removeClass('CoreShopOrder');
            $install->removeClass('CoreShopPayment');
            $install->removeClass('CoreShopOrderItem');

            $install->removeFieldcollection('CoreShopUserAddress');
            $install->removeImageThumbnails();
            $install->removeConfig();

            \Pimcore::getEventManager()->trigger('coreshop.uninstall.post', null, array('installer' => $install));

            return self::getTranslate()->_('coreshop_uninstalled_successfully');
        } catch (Exception $e) {
            \Logger::crit($e);

            return self::getTranslate()->_('coreshop_uninstall_failed');
        }
    }

    /**
     * Check if CoreShop is installed.
     *
     * @return bool $isInstalled
     */
    public static function isInstalled()
    {
        $config = Configuration::get('SYSTEM.ISINSTALLED');

        if (!is_null($config)) {
            return true;
        }

        return false;
    }

    /**
     * get translation directory.
     *
     * @return string
     */
    public static function getTranslationFileDirectory()
    {
        return PIMCORE_PLUGINS_PATH.'/CoreShop/static/texts';
    }

    /**
     * get translation file.
     *
     * @param string $language
     *
     * @return string path to the translation file relative to plugin directory
     */
    public static function getTranslationFile($language)
    {
        if (is_file(self::getTranslationFileDirectory()."/$language.csv")) {
            return "/CoreShop/static/texts/$language.csv";
        } else {
            return '/CoreShop/static/texts/en.csv';
        }
    }

    /**
     * get translate.
     *
     * @param $lang
     *
     * @return \Zend_Translate
     */
    public static function getTranslate($lang = null)
    {
        if (self::$_translate instanceof \Zend_Translate) {
            return self::$_translate;
        }
        if (is_null($lang)) {
            try {
                $lang = \Zend_Registry::get('Zend_Locale')->getLanguage();
            } catch (Exception $e) {
                $lang = 'en';
            }
        }

        self::$_translate = new \Zend_Translate(
            'csv',
            PIMCORE_PLUGINS_PATH.self::getTranslationFile($lang),
            $lang,
            array('delimiter' => ',')
        );

        return self::$_translate;
    }

    /**
     * Default Layout.
     *
     * @var string
     */
    private static $layout = 'shop';

    /**
     * Get CoreShop default layout.
     *
     * @return string
     */
    public static function getLayout()
    {
        return self::$layout;
    }

    /**
     * Set CoreShop default layout.
     *
     * @param $layout
     */
    public static function setLayout($layout)
    {
        self::$layout = $layout;
    }

    /**
     * Get PaymentProviders.
     *
     * @param Cart $cart
     *
     * @return array
     */
    public static function getPaymentProviders(Cart $cart = null)
    {
        $results = \Pimcore::getEventManager()->trigger('coreshop.payment.getProvider');
        $provider = array();

        foreach ($results as $result) {
            if ($result instanceof Payment) {
                if ($cart instanceof Cart) {
                    if ($result->isAvailable($cart)) {
                        $provider[] = $result;
                    }
                } else {
                    $provider[] = $result;
                }
            }
        }

        return $provider;
    }

    /**
     * Get PaymentProvider by identifier.
     *
     * @param $identifier
     *
     * @return bool
     */
    public static function getPaymentProvider($identifier)
    {
        $providers = self::getPaymentProviders(null);

        foreach ($providers as $provider) {
            if ($provider->getIdentifier() == $identifier) {
                return $provider;
            }
        }

        return false;
    }

    /**
     * Call a CoreShop hook.
     *
     * @param $name
     * @param array $params
     *
     * @return string
     *
     * @throws \Zend_Exception
     */
    public static function hook($name, $params = array())
    {
        $results = \Pimcore::getEventManager()->trigger('coreshop.hook.'.$name, null, array());

        $params['language'] = \Zend_Registry::get('Zend_Locale');

        if (count($results) > 0) {
            $return = array();

            foreach ($results as $result) {
                $return[] = $result->render($params);
            }

            return implode($return, "\n");
        }

        return false;
    }

    /**
     * Call an action hook.
     *
     * @param $name
     * @param array $params
     *
     * @return mixed
     *
     * @throws \Zend_Exception
     */
    public static function actionHook($name, $params = array())
    {
        $results = \Pimcore::getEventManager()->trigger('coreshop.actionHook.'.$name, null, array(), function ($v) {
            return is_callable($v);
        });

        $params['language'] = \Zend_Registry::get('Zend_Locale');

        if ($results->stopped()) {
            foreach ($results as $result) {
                if ($r = call_user_func($result)) {
                    return $r;
                }
            }
        }

        return false;
    }
}
