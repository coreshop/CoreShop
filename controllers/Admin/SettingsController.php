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

use CoreShop\Model;
use CoreShop\Controller\Action\Admin;

/**
 * Class CoreShop_Admin_SettingsController
 */
class CoreShop_Admin_SettingsController extends Admin
{
    public function init()
    {
        parent::init();

        // check permissions
        $notRestrictedActions = array('get-settings');
        if (!in_array($this->getParam('action'), $notRestrictedActions)) {
            $this->checkPermission('coreshop_permission_settings');
        }
    }

    public function getSettingsAction()
    {
        $shops = Model\Shop::getList();
        $valueArray = [];
        $systemSettings = [];

        foreach($shops as $shop) {
            $config = new Model\Configuration\Listing();
            $config->setFilter(function ($entry) use ($shop) {
                if (startsWith($entry['key'], 'SYSTEM.') && ($entry['shop'] === null || $entry['shop'] === $shop->getId())) {
                    return true;
                }

                return false;
            });

            foreach ($config->getConfigurations() as $c) {
                if(in_array($c->getKey(), Model\Configuration::getSystemKeys())) {
                    $systemSettings[$c->getKey()] = $c->getData();
                }
                else {
                    $valueArray[$shop->getId()][$c->getKey()] = $c->getData();
                }
            }
        }

        $pluginConfig = \Pimcore\ExtensionManager::getPluginConfig('CoreShop');

        $classMapping = array(
            'product' => \CoreShop\Model\Product::getPimcoreObjectClass(),
            'category' => \CoreShop\Model\Category::getPimcoreObjectClass(),
            'order' => \CoreShop\Model\Order::getPimcoreObjectClass(),
            'orderItem' => \CoreShop\Model\Order\Item::getPimcoreObjectClass(),
            'cart' => \CoreShop\Model\Cart::getPimcoreObjectClass(),
            'cartItem' => \CoreShop\Model\Cart\Item::getPimcoreObjectClass(),
            'payment' => \CoreShop\Model\Order\Payment::getPimcoreObjectClass(),
            'user' => \CoreShop\Model\User::getPimcoreObjectClass(),
        );

        foreach ($classMapping as $key => &$class) {
            $class = str_replace('Pimcore\\Model\\Object\\', '', $class);
        }

        $this->_helper->json([
            'coreshop' => $valueArray,
            'plugin' => $pluginConfig['plugin'],
            'classMapping' => $classMapping,
            'multishop' => Model\Configuration::multiShopEnabled(),
            'systemSettings' => $systemSettings,
            'defaultShop' => Model\Shop::getDefaultShop()->getId()
        ]);
    }

    public function getAction()
    {
        $shops = Model\Shop::getList();
        $valueArray = [];
        $systemValues = [];
            
        foreach($shops as $shop) {
            $shopValues = [];

            $config = new Model\Configuration\Listing();
            $config->setFilter(function ($entry) use ($shop) {
                if (startsWith($entry['key'], 'SYSTEM.') && ($entry['shopId'] === null || $entry['shopId'] === intval($shop->getId()))) {
                    return true;
                }

                return false;
            });

            foreach ($config->getConfigurations() as $c) {
                if(in_array($c->getKey(), Model\Configuration::getSystemKeys())) {
                    $systemValues[$c->getKey()] = $c->getData();
                }
                else {
                    $shopValues[$c->getKey()] = $c->getData();
                }
            }

            $valueArray[$shop->getId()] = $shopValues;
        }

        $response = array(
            'values' => $valueArray,
            'systemValues' => $systemValues
        );

        $this->_helper->json($response);
        $this->_helper->json(false);
    }

    public function setAction()
    {
        $systemValues = \Zend_Json::decode($this->getParam('systemValues'));
        $values = \Zend_Json::decode($this->getParam('values'));
        $values = array_htmlspecialchars($values);
        $diff = [];

        if(Model\Configuration::multiShopEnabled()) {
            $diff = call_user_func_array("array_diff_assoc", $values);
        }


        if(Model\Configuration::multiShopEnabled()) {
            foreach ($values as $shop => $shopValues) {
                foreach ($shopValues as $key => $val) {
                    Model\Configuration::remove($key);
                }

                break;
            }
        }

        foreach($values as $shopId => $shopValues) {
            foreach($shopValues as $key => $value) {
                if(array_key_exists($key, $diff)) {
                    Model\Configuration::set($key, $value, $shopId);
                }
                else {
                    Model\Configuration::set($key, $value);
                }
            }
        }

        foreach($systemValues as $key => $value) {
            Model\Configuration::set($key, $value);
        }

        $this->_helper->json(array('success' => true));
    }
}
