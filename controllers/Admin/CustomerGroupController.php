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
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */
use CoreShop\Model\CustomerGroup;
use Pimcore\Controller\Action\Admin;

class CoreShop_Admin_CustomerGroupController extends Admin
{
    public function init()
    {
        parent::init();

        // check permissions
        $notRestrictedActions = array('list');

        if (!in_array($this->getParam('action'), $notRestrictedActions)) {
            $this->checkPermission('coreshop_permission_customer_groups');
        }
    }

    public function listAction()
    {
        $list = new CustomerGroup\Listing();

        $data = array();
        if (is_array($list->getData())) {
            foreach ($list->getData() as $group) {
                $data[] = $this->getTreeNodeConfig($group);
            }
        }
        $this->_helper->json($data);
    }

    protected function getTreeNodeConfig(CustomerGroup $group)
    {
        $tmp = array(
            'id' => $group->getId(),
            'text' => $group->getName(),
            'elementType' => 'group',
            'qtipCfg' => array(
                'title' => 'ID: '.$group->getId(),
            ),
            'name' => $group->getName(),
        );

        $tmp['leaf'] = true;
        $tmp['iconCls'] = 'coreshop_icon_customer_groups';
        $tmp['allowChildren'] = false;

        return $tmp;
    }

    public function addAction()
    {
        $name = $this->getParam('name');

        if (strlen($name) <= 0) {
            $this->helper->json(array('success' => false, 'message' => $this->getTranslator()->translate('Name must be set')));
        } else {
            $group = new CustomerGroup();
            $group->setName($name);
            $group->setDiscount(0);
            $group->save();

            $this->_helper->json(array('success' => true, 'data' => $group));
        }
    }

    public function getAction()
    {
        $id = $this->getParam('id');
        $group = CustomerGroup::getById($id);

        if ($group instanceof CustomerGroup) {
            $this->_helper->json(array('success' => true, 'data' => $group));
        } else {
            $this->_helper->json(array('success' => false));
        }
    }

    public function saveAction()
    {
        $id = $this->getParam('id');
        $data = $this->getParam('data');
        $group = CustomerGroup::getById($id);

        if ($data && $group instanceof CustomerGroup) {
            $data = \Zend_Json::decode($this->getParam('data'));

            $group->setValues($data);
            $group->save();

            $this->_helper->json(array('success' => true, 'data' => $group));
        } else {
            $this->_helper->json(array('success' => false));
        }
    }

    public function deleteAction()
    {
        $id = $this->getParam('id');
        $group = CustomerGroup::getById($id);

        if ($group instanceof CustomerGroup) {
            $group->delete();

            $this->_helper->json(array('success' => true));
        }

        $this->_helper->json(array('success' => false));
    }
}
