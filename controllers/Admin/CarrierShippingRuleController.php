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

use Pimcore\Controller\Action\Admin;
use Pimcore\Tool as PimTool;

/**
 * Class CoreShop_Admin_CarrierShippingRuleController
 */
class CoreShop_Admin_CarrierShippingRuleController extends Admin
{
    public function init()
    {
        parent::init();

        // check permissions
        $notRestrictedActions = array('list');
        if (!in_array($this->getParam('action'), $notRestrictedActions)) {
            //TODO: $this->checkPermission("coreshop_permission_productPriceRules");
        }
    }

    public function listAction()
    {
        $list = \CoreShop\Model\Carrier\ShippingRule::getList();
        $rules = $list->load();
        $data = [];

        foreach ($rules as $rule) {
            $data[] = $this->getPriceRuleTreeNodeConfig($rule);
        }

        $this->_helper->json($data);
    }

    protected function getPriceRuleTreeNodeConfig($price)
    {
        $tmpRule = array(
            'id' => $price->getId(),
            'text' => $price->getName(),
            'qtipCfg' => array(
                'title' => 'ID: '.$price->getId(),
            ),
            'name' => $price->getName(),
        );

        return $tmpRule;
    }

    public function getConfigAction()
    {
        $this->_helper->json(array(
            'success' => true,
            'conditions' => \CoreShop\Model\Carrier\ShippingRule::$availableConditions,
            'actions' => \CoreShop\Model\Carrier\ShippingRule::$availableActions,
        ));
    }

    public function addAction()
    {
        $name = $this->getParam('name');

        $shippingRule = new \CoreShop\Model\Carrier\ShippingRule();
        $shippingRule->setName($name);
        $shippingRule->save();

        $this->_helper->json(array('success' => true, 'data' => $shippingRule));
    }

    public function getAction()
    {
        $id = $this->getParam('id');
        $specificPrice = \CoreShop\Model\Carrier\ShippingRule::getById($id);

        if ($specificPrice instanceof \CoreShop\Model\Carrier\ShippingRule) {
            $this->_helper->json(array('success' => true, 'data' => $specificPrice->getObjectVars()));
        } else {
            $this->_helper->json(array('success' => false));
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
            $actionInstances = array();
            $conditionInstances = array();

            $actionNamespace = 'CoreShop\\Model\\Carrier\\ShippingRule\\Action\\';
            $conditionNamespace = 'CoreShop\\Model\\Carrier\\ShippingRule\\Condition\\';

            foreach ($conditions as $condition) {
                $class = $conditionNamespace.ucfirst($condition['type']);

                if (PimTool::classExists($class)) {
                    $instance = new $class();
                    $instance->setValues($condition);

                    $conditionInstances[] = $instance;
                } else {
                    throw new \CoreShop\Exception(sprintf('Condition with type %s not found'), $condition['type']);
                }
            }

            foreach ($actions as $action) {
                $class = $actionNamespace.ucfirst($action['type']);

                if (PimTool::classExists($class)) {
                    $instance = new $class();
                    $instance->setValues($action);

                    $actionInstances[] = $instance;
                } else {
                    throw new \CoreShop\Exception(sprintf('Action with type %s not found'), $action['type']);
                }
            }

            $shippingRule->setValues($data['settings']);
            $shippingRule->setActions($actionInstances);
            $shippingRule->setConditions($conditionInstances);
            $shippingRule->save();

            \Pimcore\Cache::clearTag('coreshop_product_price');

            $this->_helper->json(array('success' => true, 'data' => $shippingRule));
        } else {
            $this->_helper->json(array('success' => false));
        }
    }

    public function deleteAction()
    {
        $id = $this->getParam('id');
        $shippingRule = \CoreShop\Model\Carrier\ShippingRule::getById($id);

        if ($shippingRule instanceof \CoreShop\Model\Carrier\ShippingRule) {
            $shippingRule->delete();

            $this->_helper->json(array('success' => true));
        }

        $this->_helper->json(array('success' => false));
    }
}
