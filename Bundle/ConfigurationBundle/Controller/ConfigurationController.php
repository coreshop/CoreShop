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

namespace CoreShop\Bundle\ConfigurationBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Bundle\CoreShopLegacyBundle\Model;
use CoreShop\Bundle\StoreBundle\Locator\StoreLocatorInterface;
use CoreShop\Component\Core\Helper\ArrayHelper;
use CoreShop\Component\Core\Repository\RepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use Symfony\Component\HttpFoundation\Request;
use CoreShop\Component\Core\Helper\StringHelper;

/**
 * Class AdminController
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Controller
 */
class ConfigurationController extends ResourceController
{
    public function getSettingsAction(Request $request)
    {
        $result = [];
        $stores = $this->getStoreRepository()->getList()->load();
        $valueArray = [];
        $systemSettings = [];

        foreach ($stores as $store) {
            if ($store instanceof StoreInterface) {
                $config = $this->repository->getList();
                $config->setFilter(function ($entry) use ($store) {
                    if (StringHelper::startsWith($entry['key'], 'SYSTEM.') && ($entry['shopId'] === null || $entry['shopId'] === $store->getId())) {
                        return true;
                    }

                    return false;
                });

                $configurations = $config->getData();

                if (is_array($configurations)) {
                    foreach ($configurations as $c) {
                        if (in_array($c->getKey(), Model\Configuration::getSystemKeys())) {
                            $systemSettings[$c->getKey()] = $c->getData();
                        } else {
                            $valueArray[$store->getId()][$c->getKey()] = $c->getData();
                        }
                    }
                }
            }
        }

        $result['systemSettings'] = $systemSettings;

        if ($this->configurationHelper->get("SYSTEM.ISINSTALLED")) {
            $pimcoreClasses = $this->getParameter('coreshop.resources');
            $classMapping = [];

            foreach ($pimcoreClasses as $key=>$value) {
                if ($value['classes']['is_pimcore_class']) {
                    $classMapping[$key] = $value['classes']['model'];
                }
            }

            foreach ($classMapping as $key => &$class) {
                $class = str_replace('Pimcore\\Model\\Object\\', '', $class);
                $class = str_replace('\\', '', $class);
            }

            $result['classMapping'] = $classMapping;
            $result['multishop'] = $this->configurationHelper->isMultiStoreEnabled();
            $result['defaultShop'] = $this->getStoreLocator()->getDefaultStore()->getId();
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

    public function getAction(Request $request)
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

    public function setAction(Request $request)
    {
        $systemValues = \Zend_Json::decode($request->get('systemValues'));
        $values = \Zend_Json::decode($request->get('values'));
        $values = array_htmlspecialchars($values);
        $diff = [];

        if ($this->configurationHelper->isMultiStoreEnabled()) {
            $diff = call_user_func_array(array(ArrayHelper::class, "array_diff_assoc_recursive"), $values);
        }


        if (Model\Configuration::multiShopEnabled()) {
            foreach ($values as $shop => $shopValues) {
                foreach ($shopValues as $key => $val) {
                    Model\Configuration::remove($key);
                }

                break;
            }
        }

        foreach ($values as $storeId => $storeValues) {
            foreach ($storeValues as $key => $value) {
                if (array_key_exists($key, $diff)) {
                    $this->configurationHelper->set($key, $value, $storeId);
                    Model\Configuration::set($key, $value, $storeId);
                } else {
                    $this->configurationHelper->set($key, $value);
                }
            }
        }

        foreach ($systemValues as $key => $value) {
            Model\Configuration::set($key, $value);
        }

        return $this->json(['success' => true]);
    }

    /**
     * @return RepositoryInterface
     */
    public function getStoreRepository() {
        return $this->get("coreshop.repository.store");
    }

    /**
     * @return StoreLocatorInterface
     */
    public function getStoreLocator() {
        return $this->get("coreshop.store.locator");
    }
}
