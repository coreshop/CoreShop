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

use CoreShop\Model\Manufacturer;
use CoreShop\Controller\Action\Admin;

/**
 * Class CoreShop_Admin_ManufacturerController
 */
class CoreShop_Admin_ManufacturerController extends Admin
{
    public function init()
    {
        parent::init();

        // check permissions
        $notRestrictedActions = array('list');

        if (!in_array($this->getParam('action'), $notRestrictedActions)) {
            $this->checkPermission('coreshop_permission_manufacturers');
        }
    }

    public function listAction()
    {
        $list = Manufacturer::getList();

        $data = array();
        if (is_array($list->getData())) {
            foreach ($list->getData() as $manufacturer) {
                $data[] = $this->getTreeNodeConfig($manufacturer);
            }
        }
        $this->_helper->json($data);
    }

    protected function getTreeNodeConfig(Manufacturer $manufacturer)
    {
        $tmp = array(
            'id' => $manufacturer->getId(),
            'text' => $manufacturer->getName(),
            'qtipCfg' => array(
                'title' => 'ID: '.$manufacturer->getId(),
            ),
            'name' => $manufacturer->getName(),
        );

        return $tmp;
    }

    public function addAction()
    {
        $name = $this->getParam('name');

        if (strlen($name) <= 0) {
            $this->helper->json(array('success' => false, 'message' => $this->getTranslator()->translate('Name must be set')));
        } else {
            $manufacturer = new Manufacturer();
            $manufacturer->setName($name);
            $manufacturer->save();

            $this->_helper->json(array('success' => true, 'data' => $manufacturer));
        }
    }

    public function getAction()
    {
        $id = $this->getParam('id');
        $manufacturer = Manufacturer::getById($id);

        if ($manufacturer instanceof Manufacturer) {
            $this->_helper->json(array('success' => true, 'data' => $manufacturer->getObjectVars()));
        } else {
            $this->_helper->json(array('success' => false));
        }
    }

    public function saveAction()
    {
        $id = $this->getParam('id');
        $data = $this->getParam('data');
        $manufacturer = Manufacturer::getById($id);

        if ($data && $manufacturer instanceof Manufacturer) {
            $data = \Zend_Json::decode($this->getParam('data'));

            $manufacturer->setValues($data);
            $manufacturer->save();

            $this->_helper->json(array('success' => true, 'data' => $manufacturer->getObjectVars()));
        } else {
            $this->_helper->json(array('success' => false));
        }
    }

    public function deleteAction()
    {
        $id = $this->getParam('id');
        $manufacturer = Manufacturer::getById($id);

        if ($manufacturer instanceof Manufacturer) {
            $manufacturer->delete();

            $this->_helper->json(array('success' => true));
        }

        $this->_helper->json(array('success' => false));
    }
}
