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

use CoreShop\Plugin;
use CoreShop\Model;
use Pimcore\Controller\Action\Admin;

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
        $config = new Model\Configuration\Listing();
        $config->setFilter(function ($entry) {
            if (startsWith($entry['key'], 'SYSTEM.')) {
                return true;
            }

            return false;
        });

        $valueArray = array();

        foreach ($config->getConfigurations() as $c) {
            $valueArray[$c->getKey()] = $c->getData();
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
            'multishop' => Model\Configuration::multiShopEnabled()
        ]);
    }

    public function getAction()
    {
        $config = new Model\Configuration\Listing();
        $config->setFilter(function ($entry) {
            if (startsWith($entry['key'], 'SYSTEM.')) {
                return true;
            }

            return false;
        });

        $valueArray = array();

        foreach ($config->getConfigurations() as $c) {
            $valueArray[$c->getKey()] = $c->getData();
        }

        $response = array(
            'values' => $valueArray,
        );

        $this->_helper->json($response);
        $this->_helper->json(false);
    }

    public function setAction()
    {
        $values = \Zend_Json::decode($this->getParam('data'));

        // convert all special characters to their entities so the xml writer can put it into the file
        $values = array_htmlspecialchars($values);

        foreach ($values as $key => $value) {
            Model\Configuration::set($key, $value);

            if ($key === "SYSTEM.BASE.PRICES.GROSS") {
                \Pimcore\Cache::clearAll();
            }
        }

        $this->_helper->json(array('success' => true));
    }
}
