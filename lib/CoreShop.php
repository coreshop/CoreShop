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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */


use CoreShop\Model\Configuration;
use CoreShop\Controller;
use CoreShop\Model\Currency\ExchangeRates;
use CoreShop\Model\Product;
use CoreShop\Model\Cart;
use CoreShop\Model\Plugin\Payment;
use CoreShop\Tools;
use CoreShop\IndexService;
use CoreShop\Model\TaxRule\VatManager;
use DI\ContainerBuilder;
use Pimcore\Model\Schedule\Maintenance\Job;
use Pimcore\Model\Schedule\Manager\Procedural;

/**
 * Class CoreShop
 */
class CoreShop
{
    /**
     * @var static
     */
    private static $instance;

    /**
     * @var \CoreShop\Plugin
     */
    private $plugin;

    /**
     * Default Layout.
     *
     * @var string
     */
    private $layout = 'shop';

    /**
     * @var Tools
     */
    private static $tools;

    /**
     * @param \CoreShop\Plugin $plugin
     *
     * CoreShop constructor.
     */
    public function __construct($plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @return CoreShop|Tools
     */
    public static function getTools()
    {
        if(!isset(self::$tools)) {
            self::$tools = Tools::createObject(Tools::class);
        }

        return self::$tools;
    }

    /**
     * @param $plugin
     */
    public static function bootstrap($plugin)
    {
        self::$instance = Tools::createObject(\CoreShop::class, [$plugin]);
        self::$instance->doBootstrap();
    }

    /**
     * Checks if CoreShop is already bootstrapped
     *
     * @return bool
     */
    public static function isBootstrapped()
    {
        return self::$instance instanceof static;
    }

    /**
     * @return static
     */
    public static function getInstance()
    {
        return self::$instance;
    }


    /**
     * Get CoreShop default layout.
     *
     * @return string
     */
    public static function getLayout()
    {
        return self::getInstance()->layout;
    }

    /**
     * Set CoreShop default layout.
     *
     * @param $layout
     */
    public static function setLayout($layout)
    {
        self::getInstance()->layout = $layout;
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
     *
     * @deprecated will be removed in Version 1.2
     */
    public static function hook($name, $params = array())
    {
        $params['language'] = static::getTools()->getLocale();

        $results = \Pimcore::getEventManager()->trigger('coreshop.hook.'.$name, null, $params);

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
        $params['language'] = static::getTools()->getLocale();

        $results = \Pimcore::getEventManager()->trigger('coreshop.actionHook.'.$name, null, $params, function ($v) {
            return is_callable($v);
        });

        if ($results->stopped()) {
            foreach ($results as $result) {
                if ($r = call_user_func($result)) {
                    return $r;
                }
            }
        }

        return false;
    }

    /**
     * return all available Pimcore classes and its mapped Pimcore Class Name
     *
     * @return array
     */
    public static function getPimcoreClasses() {
        return [
            'product' => [
                "pimcoreClass" => \CoreShop\Model\Product::getPimcoreObjectClass(),
                "pimcoreClassId" => \CoreShop\Model\Product::classId(),
                "coreShopClass" => \CoreShop\Model\Product::class
            ],
            'category' => [
                "pimcoreClass" => \CoreShop\Model\Category::getPimcoreObjectClass(),
                "pimcoreClassId" => \CoreShop\Model\Category::classId(),
                "coreShopClass" => \CoreShop\Model\Category::class
            ],
            'order' => [
                "pimcoreClass" => \CoreShop\Model\Order::getPimcoreObjectClass(),
                "pimcoreClassId" => \CoreShop\Model\Category::classId(),
                "coreShopClass" => \CoreShop\Model\Category::class
            ],
            'orderItem' => [
                "pimcoreClass" => \CoreShop\Model\Order\Item::getPimcoreObjectClass(),
                "pimcoreClassId" => \CoreShop\Model\Category::classId(),
                "coreShopClass" => \CoreShop\Model\Category::class
            ],
            'cart' => [
                "pimcoreClass" => \CoreShop\Model\Cart::getPimcoreObjectClass(),
                "pimcoreClassId" => \CoreShop\Model\Cart::classId(),
                "coreShopClass" => \CoreShop\Model\Cart::class
            ],
            'cartItem' => [
                "pimcoreClass" => \CoreShop\Model\Cart\Item::getPimcoreObjectClass(),
                "pimcoreClassId" => \CoreShop\Model\Cart\Item::classId(),
                "coreShopClass" => \CoreShop\Model\Cart\Item::class
            ],
            'payment' => [
                "pimcoreClass" => \CoreShop\Model\Order\Payment::getPimcoreObjectClass(),
                "pimcoreClassId" => \CoreShop\Model\Order\Payment::classId(),
                "coreShopClass" => \CoreShop\Model\Order\Payment::class
            ],
            'user' => [
                "pimcoreClass" => \CoreShop\Model\User::getPimcoreObjectClass(),
                "pimcoreClassId" => \CoreShop\Model\User::classId(),
                "coreShopClass" => \CoreShop\Model\User::class
            ],
            'customerGroup' => [
                "pimcoreClass" => \CoreShop\Model\Customer\Group::getPimcoreObjectClass(),
                "pimcoreClassId" => \CoreShop\Model\Customer\Group::classId(),
                "coreShopClass" => \CoreShop\Model\Customer\Group::class
            ],
            'invoice' => [
                "pimcoreClass" => \CoreShop\Model\Order\Invoice::getPimcoreObjectClass(),
                "pimcoreClassId" => \CoreShop\Model\Order\Invoice::classId(),
                "coreShopClass" => \CoreShop\Model\Order\Invoice::class
            ],
            'invoiceItem' => [
                "pimcoreClass" => \CoreShop\Model\Order\Invoice\Item::getPimcoreObjectClass(),
                "pimcoreClassId" => \CoreShop\Model\Order\Invoice\Item::classId(),
                "coreShopClass" => \CoreShop\Model\Order\Invoice\Item::class
            ],
            'shipment' => [
                "pimcoreClass" => \CoreShop\Model\Order\Shipment::getPimcoreObjectClass(),
                "pimcoreClassId" => \CoreShop\Model\Order\Shipment::classId(),
                "coreShopClass" => \CoreShop\Model\Order\Shipment::class
            ],
            'shipmentItem' => [
                "pimcoreClass" => \CoreShop\Model\Order\Shipment\Item::getPimcoreObjectClass(),
                "pimcoreClassId" => \CoreShop\Model\Order\Shipment\Item::classId(),
                "coreShopClass" => \CoreShop\Model\Order\Shipment\Item::class
            ]
        ];
    }

    /**
     * Bootstrap CoreShop
     */
    private function doBootstrap()
    {
        $this->createDefines();

        \Pimcore::getEventManager()->trigger("coreshop.preBootstrap");

        \Pimcore::getEventManager()->attach('system.console.init', function (\Zend_EventManager_Event $e) {
            /** @var \Pimcore\Console\Application $application */
            $application = $e->getTarget();

            // add a namespace to autoload commands from
            $application->addAutoloadNamespace('CoreShop\\Console', CORESHOP_PATH . '/lib/CoreShop/Console');
        });

        \Pimcore::getEventManager()->attach('system.startup', function (\Zend_EventManager_Event $e) {
            $frontController = $e->getTarget();

            if ($frontController instanceof \Zend_Controller_Front) {
                $frontController->registerPlugin(new Controller\Plugin\TemplateRouter());
                $frontController->registerPlugin(new Controller\Plugin\Debug());

                if(Configuration::get("SYSTEM.VISITORS.TRACK")) {
                    $frontController->registerPlugin(new Controller\Plugin\Visitor());
                }
            }
        });

        \Pimcore::getEventManager()->attach('system.console.init', function (\Zend_EventManager_Event $e) {

            $autoloader = \Zend_Loader_Autoloader::getInstance();
            $autoloader->registerNamespace('CoreShopTemplate');

            $includePaths = array(
                get_include_path(),
                CORESHOP_TEMPLATE_PATH . '/controllers',
                CORESHOP_TEMPLATE_PATH . '/lib',
            );
            set_include_path(implode(PATH_SEPARATOR, $includePaths) . PATH_SEPARATOR);

        });

        \Pimcore::getEventManager()->attach('system.maintenance', function (\Zend_EventManager_Event $e) {
            $manager = $e->getTarget();

            if ($manager instanceof Procedural) {
                if (Configuration::get('SYSTEM.CURRENCY.AUTO_EXCHANGE_RATES')) {
                    $manager->registerJob(new Job('coreshop_exchangerates', ExchangeRates::getInstance(), 'maintenance'));
                }
                if (Configuration::get('SYSTEM.CART.AUTO_CLEANUP')) {
                    $manager->registerJob(new Job('coreshop_cart_cleanup', '\\CoreShop\\Model\\Cart', 'maintenance'));
                }
                if (Configuration::get('SYSTEM.LOG.USAGESTATISTICS')) {
                    $manager->registerJob(new Job('coreshop_send_usage_statistcs', '\\CoreShop\\Maintenance\\Log', 'maintenance'));
                }
                if(Configuration::get("SYSTEM.VISITORS.TRACK")) {
                    $manager->registerJob(new Job('coreshop_send_usage_statistcs', '\\CoreShop\\Model\\Visitor', 'maintenance'));
                }
            }
        });

        //\Pimcore::getEventManager()->attach('object.postAdd', array($this, 'postAddObject'));
        \Pimcore::getEventManager()->attach('object.postUpdate', array($this, 'postUpdateObject'));
        \Pimcore::getEventManager()->attach('object.postDelete', array($this, 'postDeleteObject'));
        \Pimcore::getEventManager()->attach('object.postDelete', array($this, 'preDeleteObject'));

        \Pimcore::getEventManager()->attach("system.di.init", function (\Zend_EventManager_Event $e) {
            $diBuilder = $e->getTarget();

            if ($diBuilder instanceof ContainerBuilder) {
                $diBuilder->addDefinitions(CORESHOP_PATH . "/config/di.php");
            }
        });

        //Allows to load classes with CoreShop namespace from Website (eg. for overriding classes)
        $includePaths = array(
            get_include_path(),
            PIMCORE_WEBSITE_PATH . '/lib/CoreShop',
        );
        set_include_path(implode(PATH_SEPARATOR, $includePaths));

        if (Configuration::get('SYSTEM.BASE.DISABLEVATFORBASECOUNTRY')) {
            \Pimcore::getEventManager()->attach('coreshop.tax.getTaxManager', function () {
                return new VatManager();
            });
        }

        \Zend_Controller_Action_HelperBroker::addPath(CORESHOP_PATH.'/lib/CoreShop/Controller/Action/Helper', 'CoreShop\Controller\Action\Helper');

        \Pimcore::getEventManager()->trigger("coreshop.postBootstrap");
    }

    /**
     * Creates all defines
     */
    protected function createDefines()
    {
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
            try {
                $indexService = IndexService::getIndexService();
                $indexService->updateIndex($object);
            }
            catch(\Exception $ex) {
                \Pimcore\Logger::error($ex);
            }

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
            try {
                $indexService = IndexService::getIndexService();
                $indexService->deleteFromIndex($object);
            }
            catch(\Exception $ex) {
                \Pimcore\Logger::error($ex);
            }
        }
    }

    /**
     * Post Delete Object
     *
     * @param \Zend_EventManager_Event $e
     */
    public function postDeleteObject(\Zend_EventManager_Event $e)
    {
        $object = $e->getTarget();
        if ($object instanceof Product) {
            $prices = Product\SpecificPrice::getSpecificPrices($object);

            foreach ($prices as $pr) {
                $pr->delete();
            }
        }
    }
}
