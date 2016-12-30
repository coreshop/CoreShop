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

use CoreShop\Controller\Action\Admin;
use CoreShop\Model\Mail\Rule;
use Pimcore\Tool as PimTool;

/**
 * Class CoreShop_Admin_MailRuleController
 */
class CoreShop_Admin_MailRuleController extends Admin
{
    public function init()
    {
        parent::init();

        // check permissions
        /*$notRestrictedActions = ['list'];
        if (!in_array($this->getParam('action'), $notRestrictedActions)) {
            $this->checkPermission("coreshop_permission_carriers");
        }*/
    }

    public function listAction()
    {
        $list = \CoreShop\Model\Mail\Rule::getList();
        $rules = $list->load();
        $data = [];

        foreach ($rules as $rule) {
            $data[] = $this->getMailRuleTreeNodeConfig($rule);
        }

        $this->_helper->json($data);
    }

    /**
     * @param $rule
     *
     * @return array
     */
    protected function getMailRuleTreeNodeConfig($rule)
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
        $conditions = [];
        $actions = [];

        foreach(Rule::$availableTypes as $type) {
            $conditions[$type] = Rule::getConditionDispatcherForType($type)->getTypeKeys();
            $actions[$type] = Rule::getActionDispatcherForType($type)->getTypeKeys();
        }

        $this->_helper->json([
            'success' => true,
            'types' => \CoreShop\Model\Mail\Rule::$availableTypes,
            'conditions' => $conditions,
            'actions' => $actions,
        ]);
    }

    public function getExternalEventsAction()
    {
        $events = Rule\Event\EventDispatcher::getExternalEvents();
        $this->_helper->json(['data' => $events]);
    }

    public function addAction()
    {
        $name = $this->getParam('name');

        $rule = \CoreShop\Model\Mail\Rule::create();
        $rule->setName($name);
        $rule->save();

        $this->_helper->json(['success' => true, 'data' => $rule]);
    }

    public function getAction()
    {
        $id = $this->getParam('id');
        $rule = \CoreShop\Model\Mail\Rule::getById($id);

        if ($rule instanceof \CoreShop\Model\Mail\Rule) {
            $this->_helper->json(['success' => true, 'data' => $rule->serialize()]);
        } else {
            $this->_helper->json(['success' => false]);
        }
    }

    public function saveAction()
    {
        $id = $this->getParam('id');
        $data = $this->getParam('data');
        $rule = \CoreShop\Model\Mail\Rule::getById($id);

        if ($data && $rule instanceof \CoreShop\Model\Mail\Rule) {
            $data = \Zend_Json::decode($this->getParam('data'));

            $conditions = $data['conditions'];
            $actions = $data['actions'];

            $actionInstances = $rule->prepareActions($actions);
            $conditionInstances = $rule->prepareConditions($conditions);

            $rule->setValues($data['settings']);
            $rule->setActions($actionInstances);
            $rule->setConditions($conditionInstances);
            $rule->save();

            $this->_helper->json(['success' => true, 'data' => $rule]);
        } else {
            $this->_helper->json(['success' => false]);
        }
    }

    public function deleteAction()
    {
        $id = $this->getParam('id');
        $shippingRule = \CoreShop\Model\Mail\Rule::getById($id);

        if ($shippingRule instanceof \CoreShop\Model\Mail\Rule) {
            $shippingRule->delete();

            $this->_helper->json(['success' => true]);
        }

        $this->_helper->json(['success' => false]);
    }
}
