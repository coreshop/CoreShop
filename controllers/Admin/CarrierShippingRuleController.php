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

use CoreShop\Controller\Action\Admin;

/**
 * Class CoreShop_Admin_CarrierShippingRuleController
 */
class CoreShop_Admin_CarrierShippingRuleController extends Admin
{
    public function init()
    {
        parent::init();

        // check permissions
        $notRestrictedActions = ['list'];
        if (!in_array($this->getParam('action'), $notRestrictedActions)) {
            $this->checkPermission("coreshop_permission_carriers");
        }
    }

    public function listAction()
    {
        $list = \CoreShop\Model\Carrier\ShippingRule::getList();
        $rules = $list->load();
        $data = [];

        foreach ($rules as $rule) {
            $data[] = $this->getShippingRuleTreeNodeConfig($rule);
        }

        $this->_helper->json($data);
    }

    /**
     * @param \CoreShop\Model\Carrier\ShippingRule $rule
     * @return array
     */
    protected function getShippingRuleTreeNodeConfig($rule)
    {
        $tmpRule = [
            'id' => $rule->getId(),
            'text' => $rule->getName(),
            'qtipCfg' => [
                'title' => 'ID: '.$rule->getId(),
            ],
            'name' => $rule->getName(),
        ];

        return $tmpRule;
    }

    public function getConfigAction()
    {
        $this->_helper->json([
            'success' => true,
            'conditions' => \CoreShop\Model\Carrier\ShippingRule::getConditionDispatcher()->getTypeKeys(),
            'actions' => \CoreShop\Model\Carrier\ShippingRule::getActionDispatcher()->getTypeKeys(),
        ]);
    }

    public function addAction()
    {
        $name = $this->getParam('name');

        $shippingRule = \CoreShop\Model\Carrier\ShippingRule::create();
        $shippingRule->setName($name);
        $shippingRule->save();

        $this->_helper->json(['success' => true, 'data' => $shippingRule]);
    }

    public function getAction()
    {
        $id = $this->getParam('id');
        $specificPrice = \CoreShop\Model\Carrier\ShippingRule::getById($id);

        if ($specificPrice instanceof \CoreShop\Model\Carrier\ShippingRule) {
            $this->_helper->json(['success' => true, 'data' => $specificPrice->serialize()]);
        } else {
            $this->_helper->json(['success' => false]);
        }
    }

    public function saveAction()
    {
        $id = $this->getParam('id');
        $data = $this->getParam('data');
        $shippingRule = \CoreShop\Model\Carrier\ShippingRule::getById($id);

        if ($data && $shippingRule instanceof \CoreShop\Model\Carrier\ShippingRule) {
            $data = \Zend_Json::decode($this->getParam('data'));

            $conditions = $data['conditions'];
            $actions = $data['actions'];

            $actionInstances = $shippingRule->prepareActions($actions);
            $conditionInstances = $shippingRule->prepareConditions($conditions);

            $shippingRule->setValues($data['settings']);
            $shippingRule->setActions($actionInstances);
            $shippingRule->setConditions($conditionInstances);
            $shippingRule->save();

            \Pimcore\Cache::clearTag('coreshop_product_price');

            $this->_helper->json(['success' => true, 'data' => $shippingRule]);
        } else {
            $this->_helper->json(['success' => false]);
        }
    }

    public function deleteAction()
    {
        $id = $this->getParam('id');
        $shippingRule = \CoreShop\Model\Carrier\ShippingRule::getById($id);

        if ($shippingRule instanceof \CoreShop\Model\Carrier\ShippingRule) {
            $shippingRule->delete();

            $this->_helper->json(['success' => true]);
        }

        $this->_helper->json(['success' => false]);
    }

    public function getUsedByCarriersAction()
    {
        $id = $this->getParam('id');
        $shippingRule = \CoreShop\Model\Carrier\ShippingRule::getById($id);

        if ($shippingRule instanceof \CoreShop\Model\Carrier\ShippingRule) {
            $list = \CoreShop\Model\Carrier\ShippingRuleGroup::getList();
            $list->setCondition("shippingRuleId = ?", [$id]);
            $list->load();

            $carriers = [];

            foreach ($list->getData() as $group) {
                if ($group instanceof \CoreShop\Model\Carrier\ShippingRuleGroup) {
                    $carrier = $group->getCarrier();

                    if ($carrier instanceof \CoreShop\Model\Carrier) {
                        $carriers[] = [
                            "id" => $carrier->getId(),
                            "name" => $carrier->getName()
                        ];
                    }
                }
            }

            $this->_helper->json(['success' => true, 'carriers' => $carriers]);
        }

        $this->_helper->json(['success' => false]);
    }
}
