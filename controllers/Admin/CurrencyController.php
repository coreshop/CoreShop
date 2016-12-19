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

use CoreShop\Model\Currency;
use CoreShop\Controller\Action\Admin;

/**
 * Class CoreShop_Admin_CurrencyController
 */
class CoreShop_Admin_CurrencyController extends Admin
{
    public function init()
    {
        parent::init();

        // check permissions
        $notRestrictedActions = ['list'];
        if (!in_array($this->getParam('action'), $notRestrictedActions)) {
            $this->checkPermission('coreshop_permission_currencies');
        }
    }

    public function listAction()
    {
        $list = Currency::getList();
        $list->setOrder('ASC');
        $list->load();

        $currencies = [];
        if (is_array($list->getData())) {
            foreach ($list->getData() as $currency) {
                $currencies[] = $this->getTreeNodeConfig($currency);
            }
        }
        $this->_helper->json($currencies);
    }

    /**
     * @param Currency $currency
     * @return array
     */
    protected function getTreeNodeConfig(Currency $currency)
    {
        $tmpCurrency = [
            'id' => $currency->getId(),
            'text' => $currency->getName(),
            'qtipCfg' => [
                'title' => 'ID: '.$currency->getId(),
            ],
            'name' => $currency->getName(),
            'symbol' => $currency->getSymbol(),
            'active' => $currency->getActive()
        ];

        return $tmpCurrency;
    }

    public function getAction()
    {
        $id = $this->getParam('id');
        $currency = Currency::getById($id);

        if ($currency instanceof Currency) {
            $this->_helper->json(['success' => true, 'data' => $currency]);
        } else {
            $this->_helper->json(['success' => false]);
        }
    }

    public function saveAction()
    {
        $id = $this->getParam('id');
        $data = $this->getParam('data');
        $currency = Currency::getById($id);

        if ($data && $currency instanceof Currency) {
            $data = \Zend_Json::decode($this->getParam('data'));

            $currency->setValues($data);
            $currency->save();

            $this->_helper->json(['success' => true, 'data' => $currency]);
        } else {
            $this->_helper->json(['success' => false]);
        }
    }

    public function addAction()
    {
        $name = $this->getParam('name');

        if (strlen($name) <= 0) {
            $this->helper->json(['success' => false, 'message' => $this->getTranslator()->translate('Name must be set')]);
        } else {
            $currency = Currency::create();
            $currency->setName($name);
            $currency->save();

            $this->_helper->json(['success' => true, 'data' => $currency]);
        }
    }

    public function deleteAction()
    {
        $id = $this->getParam('id');
        $currency = Currency::getById($id);

        if ($currency instanceof Currency) {
            $currency->delete();

            $this->_helper->json(['success' => true]);
        }

        $this->_helper->json(['success' => false]);
    }

    public function getExchangeRateProvidersAction()
    {
        $providersList = [];

        foreach (Currency\ExchangeRates::$providerList as $name => $class) {
            $providersList[] = ['name' => $name];
        }

        $this->_helper->json(['success' => true, 'data' => $providersList]);
    }
}
