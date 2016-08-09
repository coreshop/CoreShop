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

use CoreShop\Model\Country;
use CoreShop\Controller\Action\Admin;

/**
 * Class CoreShop_Admin_CountryController
 */
class CoreShop_Admin_CountryController extends Admin
{
    public function init()
    {
        parent::init();

        // check permissions
        $notRestrictedActions = array('list');
        if (!in_array($this->getParam('action'), $notRestrictedActions)) {
            $this->checkPermission('coreshop_permission_countries');
        }
    }

    public function listAction()
    {
        $list = CoreShop\Model\Country::getList();
        $list->setOrder('ASC');
        $list->setOrderKey('name');
        $list->load();

        $countries = array();
        if (is_array($list->getData())) {
            foreach ($list->getData() as $country) {
                $countries[] = $this->getTreeNodeConfig($country);
            }
        }
        $this->_helper->json($countries);
    }

    protected function getTreeNodeConfig(Country $country)
    {
        $tmpCountry = array(
            'id' => $country->getId(),
            'text' => $country->getName(),
            'qtipCfg' => array(
                'title' => 'ID: '.$country->getId(),
            ),
            'name' => $country->getName(),
            'zone' => $country->getZone() instanceof \CoreShop\Model\Zone ? $country->getZone()->getName() : ''
        );

        return $tmpCountry;
    }

    public function getAction()
    {
        $id = $this->getParam('id');
        $country = Country::getById($id);

        if ($country instanceof Country) {
            $this->_helper->json(array('success' => true, 'data' => $country));
        } else {
            $this->_helper->json(array('success' => false));
        }
    }

    public function saveAction()
    {
        $id = $this->getParam('id');
        $data = $this->getParam('data');
        $country = Country::getById($id);

        if ($data && $country instanceof Country) {
            $data = \Zend_Json::decode($this->getParam('data'));

            $country->setValues($data);
            $country->save();

            $this->_helper->json(array('success' => true, 'data' => $country));
        } else {
            $this->_helper->json(array('success' => false));
        }
    }

    public function addAction()
    {
        $name = $this->getParam('name');

        if (strlen($name) <= 0) {
            $this->helper->json(array('success' => false, 'message' => $this->getTranslator()->translate('Name must be set')));
        } else {
            $country = new Country();
            $country->setName($name);
            $country->setActive(1);
            $country->save();

            $this->_helper->json(array('success' => true, 'data' => $country));
        }
    }

    public function deleteAction()
    {
        $id = $this->getParam('id');
        $country = Country::getById($id);

        if ($country instanceof Country) {
            $country->delete();

            $this->_helper->json(array('success' => true));
        }

        $this->_helper->json(array('success' => false));
    }
}
