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

use CoreShop\Model\Carrier;
use Pimcore\Controller\Action\Admin;

/**
 * Class CoreShop_Admin_CarrierController
 */
class CoreShop_Admin_CarrierController extends Admin
{
    public function init()
    {
        parent::init();

        // check permissions
        $notRestrictedActions = array('list');
        if (!in_array($this->getParam('action'), $notRestrictedActions)) {
            $this->checkPermission('coreshop_permission_carriers');
        }
    }

    public function listAction()
    {
        $list = Carrier::getList();

        $data = array();
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

        $carriers = array();
        if (is_array($list->getData())) {
            foreach ($list->getData() as $carrier) {
                $carriers[] = $this->getTreeNodeConfig($carrier);
            }
        }

        $this->_helper->json($carriers);
    }

    protected function getTreeNodeConfig($carrier)
    {
        $tmpCarrier = array(
            'id' => $carrier->getId(),
            'text' => $carrier->getName(),
            'qtipCfg' => array(
                'title' => 'ID: '.$carrier->getId(),
            ),
            'name' => $carrier->getName(),
        );

        return $tmpCarrier;
    }

    public function getShippingRuleGroupsAction() {
        $id = $this->getParam('carrier');
        $carrier = Carrier::getById($id);

        if ($carrier instanceof Carrier) {
            $groups = $carrier->getShippingRuleGroups();

            $this->_helper->json(array('success' => true, 'total' => count($groups), 'data' => $groups));
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
            $carrier = new Carrier();
            $carrier->setName($name);
            $carrier->setLabel($name);
            $carrier->setGrade(1);
            $carrier->setIsFree(0);
            $carrier->setShippingMethod('weight');
            $carrier->setRangeBehaviour('largest');
            $carrier->setMaxDepth(0);
            $carrier->setMaxHeight(0);
            $carrier->setMaxWeight(0);
            $carrier->setMaxWidth(0);
            $carrier->setNeedsRange(0);
            $carrier->save();

            $config = $this->getTreeNodeConfig($carrier);
            $config['success'] = true;

            $this->_helper->json(array('success' => true, 'data' => $carrier));
        }
    }

    public function getAction()
    {
        $id = $this->getParam('id');
        $carrier = Carrier::getById($id);

        if ($carrier instanceof Carrier) {
            $this->_helper->json(array('success' => true, 'data' => $carrier));
        } else {
            $this->_helper->json(array('success' => false));
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

            foreach($oldGroups as $group) {
                $group->delete();
            }

            $carrier->setValues($data['settings']);

            foreach($data['groups'] as $group) {
                $obj = new CoreShop\Model\Carrier\ShippingRuleGroup();
                $obj->setCarrier($carrier);
                $obj->setPriority($group['priority']);
                $obj->setShippingRuleId($group['shippingRuleId']);
                $obj->save();
            }

            $carrier->save();

            $this->_helper->json(array('success' => true, 'data' => $carrier, 'shippingRuleGroups' => $carrier->getShippingRuleGroups()));
        } else {
            $this->_helper->json(array('success' => false));
        }
    }

    public function deleteAction()
    {
        $id = $this->getParam('id');
        $carrier = Carrier::getById($id);

        if ($carrier instanceof Carrier) {
            $carrier->delete();

            $this->_helper->json(array('success' => true));
        }

        $this->_helper->json(array('success' => false));
    }
}
