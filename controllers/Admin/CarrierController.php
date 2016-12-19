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

use CoreShop\Model\Carrier;
use CoreShop\Controller\Action\Admin;

/**
 * Class CoreShop_Admin_CarrierController
 */
class CoreShop_Admin_CarrierController extends Admin
{
    public function init()
    {
        parent::init();

        // check permissions
        $notRestrictedActions = ['list'];
        if (!in_array($this->getParam('action'), $notRestrictedActions)) {
            $this->checkPermission('coreshop_permission_carriers');
        }
    }

    public function listAction()
    {
        $list = Carrier::getList();

        $data = [];
        if (is_array($list->getData())) {
            foreach ($list->getData() as $carrier) {
                $data[] = $this->getTreeNodeConfig($carrier);
            }
        }
        $this->_helper->json($data);
    }

    public function getCarriersAction()
    {
        $list = Carrier::getList();
        $list->setOrder('ASC');
        $list->setOrderKey('name');
        $list->load();

        $carriers = [];
        if (is_array($list->getData())) {
            foreach ($list->getData() as $carrier) {
                $carriers[] = $this->getTreeNodeConfig($carrier);
            }
        }

        $this->_helper->json($carriers);
    }

    protected function getTreeNodeConfig($carrier)
    {
        $tmpCarrier = [
            'id' => $carrier->getId(),
            'text' => $carrier->getName(),
            'qtipCfg' => [
                'title' => 'ID: '.$carrier->getId(),
            ],
            'name' => $carrier->getName(),
        ];

        return $tmpCarrier;
    }

    public function getShippingRuleGroupsAction()
    {
        $id = $this->getParam('carrier');
        $carrier = Carrier::getById($id);

        if ($carrier instanceof Carrier) {
            $groups = $carrier->getShippingRuleGroups();

            $this->_helper->json(['success' => true, 'total' => count($groups), 'data' => $groups]);
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
            $carrier = Carrier::create();
            $carrier->setName($name);
            $carrier->setLabel($name);
            $carrier->setGrade(1);
            $carrier->setIsFree(0);
            $carrier->setRangeBehaviour('largest');
            $carrier->save();

            $config = $this->getTreeNodeConfig($carrier);
            $config['success'] = true;

            $this->_helper->json(['success' => true, 'data' => $carrier]);
        }
    }

    public function getAction()
    {
        $id = $this->getParam('id');
        $carrier = Carrier::getById($id);

        if ($carrier instanceof Carrier) {
            $this->_helper->json(['success' => true, 'data' => $carrier]);
        } else {
            $this->_helper->json(['success' => false]);
        }
    }

    public function saveAction()
    {
        $id = $this->getParam('id');
        $data = $this->getParam('data');
        $carrier = Carrier::getById($id);

        if ($data && $carrier instanceof Carrier) {
            $data = \Zend_Json::decode($this->getParam('data'));

            if ($data['settings']['image']) {
                $asset = \Pimcore\Model\Asset::getById($data['settings']['image']);

                if ($asset instanceof \Pimcore\Model\Asset) {
                    $data['settings']['image'] = $asset->getId();
                }
            }

            $oldGroups = $carrier->getShippingRuleGroups();

            foreach ($oldGroups as $group) {
                $group->delete();
            }

            $carrier->setValues($data['settings']);

            foreach ($data['groups'] as $group) {
                $obj = CoreShop\Model\Carrier\ShippingRuleGroup::create();
                $obj->setCarrier($carrier);
                $obj->setPriority($group['priority']);
                $obj->setShippingRuleId($group['shippingRuleId']);
                $obj->save();
            }

            $carrier->save();

            $this->_helper->json(['success' => true, 'data' => $carrier, 'shippingRuleGroups' => $carrier->getShippingRuleGroups()]);
        } else {
            $this->_helper->json(['success' => false]);
        }
    }

    public function deleteAction()
    {
        $id = $this->getParam('id');
        $carrier = Carrier::getById($id);

        if ($carrier instanceof Carrier) {
            $carrier->delete();

            $this->_helper->json(['success' => true]);
        }

        $this->_helper->json(['success' => false]);
    }
}
