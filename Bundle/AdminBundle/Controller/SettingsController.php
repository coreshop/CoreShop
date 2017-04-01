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

namespace CoreShop\Bundle\AdminBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\AdminController;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Core\Helper\StringHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SettingsController
 */
class SettingsController extends AdminController
{
    /**
     * @param FilterControllerEvent $event
     * @throws \Exception
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        // permission check
        $access = $this->getUser()->getPermission('coreshop_permission_settings');
        if (!$access) {
            throw new \Exception(sprintf('this function requires "%s" permission!', 'coreshop_permission_settings'));
        }
    }

    /**
     * @Route("/get-settings")
     */
    public function getSettingsAction(Request $request)
    {
        $result = [];
        $shops = Model\Shop::getList();
        $valueArray = [];
        $systemSettings = [];

        foreach ($shops as $shop) {
            $config = new Model\Configuration\Listing();
            $config->setFilter(function ($entry) use ($shop) {
                if (StringHelper::startsWith($entry['key'], 'SYSTEM.') && (array_key_exists('shop', $entry) && ($entry['shop'] === null || $entry['shop'] === $shop->getId()))) {
                    return true;
                }

                return false;
            });

            $configurations = $config->getConfigurations();

            if (is_array($configurations)) {
                foreach ($configurations as $c) {
                    if (in_array($c->getKey(), Model\Configuration::getSystemKeys())) {
                        $systemSettings[$c->getKey()] = $c->getData();
                    } else {
                        $valueArray[$shop->getId()][$c->getKey()] = $c->getData();
                    }
                }
            }
        }

        $result['systemSettings'] = $systemSettings;

        if (Model\Configuration::get("SYSTEM.ISINSTALLED")) {
            $pimcoreClasses = \CoreShop\Bundle\CoreShopLegacyBundle\CoreShop::getPimcoreClasses();
            $classMapping = [];

            foreach ($pimcoreClasses as $key=>$value) {
                $classMapping[$key] = $value['pimcoreClass'];
            }

            foreach ($classMapping as $key => &$class) {
                $class = str_replace('Pimcore\\Model\\Object\\', '', $class);
                $class = str_replace('\\', '', $class);
            }

            $result['classMapping'] = $classMapping;
            $result['multishop'] = Model\Configuration::multiShopEnabled();
            $result['defaultShop'] = Model\Shop::getDefaultShop()->getId();
            $result['coreshop'] = $valueArray;
            $result['orderStates'] = Model\Order\State::getValidOrderStates();
        }

        //TODO
        $result['plugin'] = [
            'pluginVersion' => 'TODO',
            'pluginRevision' => 'TODO'
        ];

        return $this->json($result);
    }

    /**
     * @Route("/get")
     */
    public function getAction()
    {
        $shops = Model\Shop::getList();
        $valueArray = [];
        $systemValues = [];
            
        foreach ($shops as $shop) {
            $shopValues = [];

            $config = new Model\Configuration\Listing();
            $config->setFilter(function ($entry) use ($shop) {
                if (StringHelper::startsWith($entry['key'], 'SYSTEM.') && ($entry['shopId'] === null || $entry['shopId'] === intval($shop->getId()))) {
                    return true;
                }

                return false;
            });

            $configurations = $config->getConfigurations();

            if (is_array($configurations)) {
                foreach ($configurations as $c) {
                    if (in_array($c->getKey(), Model\Configuration::getSystemKeys())) {
                        $systemValues[$c->getKey()] = $c->getData();
                    } else {
                        $shopValues[$c->getKey()] = $c->getData();
                    }
                }
            }

            $valueArray[$shop->getId()] = $shopValues;
        }

        $response = [
            'values' => $valueArray,
            'systemValues' => $systemValues
        ];

        return $this->json($response);
    }

    /**
     * @Route("/set")
     */
    public function setAction(Request $request)
    {
        $systemValues = \Zend_Json::decode($request->get('systemValues'));
        $values = \Zend_Json::decode($request->get('values'));
        $values = array_htmlspecialchars($values);
        $diff = [];

        if (Model\Configuration::multiShopEnabled()) {
            $diff = call_user_func_array("array_diff_assoc_recursive", $values);
        }


        if (Model\Configuration::multiShopEnabled()) {
            foreach ($values as $shop => $shopValues) {
                foreach ($shopValues as $key => $val) {
                    Model\Configuration::remove($key);
                }

                break;
            }
        }

        foreach ($values as $shopId => $shopValues) {
            foreach ($shopValues as $key => $value) {
                if (array_key_exists($key, $diff)) {
                    Model\Configuration::set($key, $value, $shopId);
                } else {
                    Model\Configuration::set($key, $value);
                }
            }
        }

        foreach ($systemValues as $key => $value) {
            Model\Configuration::set($key, $value);
        }

        return $this->json(['success' => true]);
    }

    /**
     * @return FactoryInterface
     */
    public function getShopFactory() {
        return $this->get("coreshop.factory.shop");
    }
}
