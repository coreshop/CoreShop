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

namespace CoreShop\Bundle\CoreShopLegacyBundle;

use CoreShop\Bundle\CoreShopLegacyBundle\Model\Configuration;
use CoreShop\Bundle\CoreShopLegacyBundle\Controller;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Currency\ExchangeRates;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Product;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Plugin\Payment;
use CoreShop\Bundle\CoreShopLegacyBundle\Tools;
use CoreShop\Bundle\CoreShopLegacyBundle\IndexService;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\TaxRule\VatManager;
use DI\ContainerBuilder;
use Pimcore\Model\Schedule\Maintenance\Job;
use Pimcore\Model\Schedule\Manager\Procedural;
use Symfony\Component\EventDispatcher\GenericEvent;

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
     * @var \CoreShop\Bundle\CoreShopLegacyBundle\CoreShopLegacyBundle
     */
    private $bundle;

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
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\CoreShopLegacyBundle $bundle
     *
     * CoreShop constructor.
     */
    public function __construct($bundle)
    {
        $this->bundle = $bundle;
    }

    /**
     * @return CoreShop|Tools
     */
    public static function getTools()
    {
        if (!isset(self::$tools)) {
            self::$tools = Tools::createObject(Tools::class);
        }

        return self::$tools;
    }

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\CoreShopLegacyBundle $bundle
     */
    public static function bootstrap($bundle)
    {
        self::$instance = Tools::createObject(static::class, [$bundle]);
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
        $results = \Pimcore::getEventDispatcher()->dispatch('coreshop.payment.getProvider');
        $provider = [];

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
     * @return null|Payment
     */
    public static function getPaymentProvider($identifier)
    {
        $providers = self::getPaymentProviders(null);

        foreach ($providers as $provider) {
            if ($provider->getIdentifier() == $identifier) {
                return $provider;
            }
        }

        return null;
    }

    /**
     * Call an action hook.
     *
     * @param string $name
     * @param array $params
     *
     * @return mixed
     *
     * @throws \Zend_Exception
     */
    public static function actionHook($name, $params = [])
    {
        //TODO:
        return false;
        $params['language'] = static::getTools()->getLocale();

        $results = \Pimcore::getEventDispatcher()->dispatch('coreshop.actionHook.'.$name, null, $params, function ($v) {
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
    public static function getPimcoreClasses()
    {
        return [
            'product' => [
                "pimcoreClass" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Product::getPimcoreObjectClass(),
                "pimcoreClassId" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Product::classId(),
                "coreShopClass" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Product::class
            ],
            'category' => [
                "pimcoreClass" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Category::getPimcoreObjectClass(),
                "pimcoreClassId" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Category::classId(),
                "coreShopClass" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Category::class
            ],
            'order' => [
                "pimcoreClass" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order::getPimcoreObjectClass(),
                "pimcoreClassId" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order::classId(),
                "coreShopClass" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order::class
            ],
            'orderItem' => [
                "pimcoreClass" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Item::getPimcoreObjectClass(),
                "pimcoreClassId" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Item::classId(),
                "coreShopClass" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Item::class
            ],
            'cart' => [
                "pimcoreClass" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart::getPimcoreObjectClass(),
                "pimcoreClassId" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart::classId(),
                "coreShopClass" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart::class
            ],
            'cartItem' => [
                "pimcoreClass" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart\Item::getPimcoreObjectClass(),
                "pimcoreClassId" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart\Item::classId(),
                "coreShopClass" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart\Item::class
            ],
            'payment' => [
                "pimcoreClass" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Payment::getPimcoreObjectClass(),
                "pimcoreClassId" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Payment::classId(),
                "coreShopClass" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Payment::class
            ],
            'user' => [
                "pimcoreClass" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\User::getPimcoreObjectClass(),
                "pimcoreClassId" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\User::classId(),
                "coreShopClass" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\User::class
            ],
            'customerGroup' => [
                "pimcoreClass" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Customer\Group::getPimcoreObjectClass(),
                "pimcoreClassId" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Customer\Group::classId(),
                "coreShopClass" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Customer\Group::class
            ],
            'invoice' => [
                "pimcoreClass" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Invoice::getPimcoreObjectClass(),
                "pimcoreClassId" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Invoice::classId(),
                "coreShopClass" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Invoice::class
            ],
            'invoiceItem' => [
                "pimcoreClass" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Invoice\Item::getPimcoreObjectClass(),
                "pimcoreClassId" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Invoice\Item::classId(),
                "coreShopClass" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Invoice\Item::class
            ],
            'shipment' => [
                "pimcoreClass" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Shipment::getPimcoreObjectClass(),
                "pimcoreClassId" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Shipment::classId(),
                "coreShopClass" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Shipment::class
            ],
            'shipmentItem' => [
                "pimcoreClass" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Shipment\Item::getPimcoreObjectClass(),
                "pimcoreClassId" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Shipment\Item::classId(),
                "coreShopClass" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order\Shipment\Item::class
            ],
            'manufacturer' => [
                "pimcoreClass" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Manufacturer::getPimcoreObjectClass(),
                "pimcoreClassId" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Manufacturer::classId(),
                "coreShopClass" => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Manufacturer::class
            ]
        ];
    }

    /**
     * Bootstrap CoreShop
     */
    private function doBootstrap()
    {
        $this->createDefines();

        \Pimcore::getEventDispatcher()->dispatch("coreshop.preBootstrap");

        \Pimcore::getEventDispatcher()->addListener('system.console.init', function (\Zend_EventManager_Event $e) {
            /** @var \Pimcore\Console\Application $application */
            $application = $e->getTarget();

            // add a namespace to autoload commands from
            $application->addAutoloadNamespace('CoreShop\Bundle\CoreShopLegacyBundle\\Console', CORESHOP_PATH . '/lib/CoreShop/Console');
        });

        \Pimcore::getEventDispatcher()->addListener('system.maintenance', function (\Zend_EventManager_Event $e) {
            $manager = $e->getTarget();

            if ($manager instanceof Procedural) {
                if (Configuration::get('SYSTEM.CURRENCY.AUTO_EXCHANGE_RATES')) {
                    $manager->registerJob(new Job('coreshop_exchangerates', ExchangeRates::getInstance(), 'maintenance'));
                }
                if (Configuration::get('SYSTEM.CART.AUTO_CLEANUP')) {
                    $manager->registerJob(new Job('coreshop_cart_cleanup', '\\CoreShop\Bundle\CoreShopLegacyBundle\\Model\\Cart', 'maintenance'));
                }
                if (Configuration::get('SYSTEM.LOG.USAGESTATISTICS')) {
                    $manager->registerJob(new Job('coreshop_send_usage_statistcs', '\\CoreShop\Bundle\CoreShopLegacyBundle\\Maintenance\\Log', 'maintenance'));
                }
                if (Configuration::get("SYSTEM.VISITORS.TRACK")) {
                    $manager->registerJob(new Job('coreshop_send_usage_statistcs', '\\CoreShop\Bundle\CoreShopLegacyBundle\\Model\\Visitor', 'maintenance'));
                }
            }
        });

        \Pimcore::getEventDispatcher()->addListener('object.postUpdate', [$this, 'postUpdateObject']);
        \Pimcore::getEventDispatcher()->addListener('object.postDelete', [$this, 'postDeleteObject']);
        \Pimcore::getEventDispatcher()->addListener('object.postDelete', [$this, 'preDeleteObject']);

        \Pimcore::getEventDispatcher()->addListener("system.di.init", function (\Zend_EventManager_Event $e) {
            $diBuilder = $e->getTarget();

            if ($diBuilder instanceof ContainerBuilder) {
                $diBuilder->addDefinitions(CORESHOP_PATH . "/config/di.php");
            }
        });


        if (Configuration::get('SYSTEM.BASE.DISABLEVATFORBASECOUNTRY')) {
            \Pimcore::getEventDispatcher()->addListener('coreshop.tax.getTaxManager', function () {
                return new VatManager();
            });
        }

        \Zend_Controller_Action_HelperBroker::addPath(CORESHOP_PATH.'/lib/CoreShop/Controller/Action/Helper', 'CoreShop\Bundle\CoreShopLegacyBundle\Controller\Action\Helper');

        \Pimcore::getEventDispatcher()->dispatch("coreshop.postBootstrap");
    }

    /**
     * Creates all defines
     */
    protected function createDefines()
    {
        if (!defined("CORESHOP_PATH")) {
            define("CORESHOP_PATH", __DIR__);
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
            } catch (\Exception $ex) {
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
            } catch (\Exception $ex) {
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
