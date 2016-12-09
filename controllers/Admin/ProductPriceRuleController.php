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
use Pimcore\Tool as PimTool;

/**
 * Class CoreShop_Admin_ProductPriceRuleController
 */
class CoreShop_Admin_ProductPriceRuleController extends Admin
{
    public function init()
    {
        parent::init();

        // check permissions
        $notRestrictedActions = array('list');
        if (!in_array($this->getParam('action'), $notRestrictedActions)) {
            $this->checkPermission("coreshop_permission_product_price_rules");
        }
    }

    public function listAction()
    {
        $list = \CoreShop\Model\Product\PriceRule::getList();
        $rules = $list->load();
        $data = [];

        foreach ($rules as $rule) {
            $data[] = $this->getPriceRuleTreeNodeConfig($rule);
        }

        $this->_helper->json(array('success' => true, 'data' => $data));
    }

    protected function getPriceRuleTreeNodeConfig($price)
    {
        $tmpPriceRule = array(
            'id' => $price->getId(),
            'text' => $price->getName(),
            'qtipCfg' => array(
                'title' => 'ID: '.$price->getId(),
            ),
            'name' => $price->getName(),
        );

        return $tmpPriceRule;
    }

    public function getConfigAction()
    {
        $this->_helper->json(array(
            'success' => true,
            'conditions' => \CoreShop\Model\Product\PriceRule::$availableConditions,
            'actions' => \CoreShop\Model\Product\PriceRule::$availableActions,
        ));
    }

    public function addAction()
    {
        $name = $this->getParam('name');

        $priceRule = \CoreShop\Model\Product\PriceRule::create();
        $priceRule->setName($name);
        $priceRule->setActive(false);
        $priceRule->save();

        $this->_helper->json(array('success' => true, 'data' => $priceRule));
    }

    public function getAction()
    {
        $id = $this->getParam('id');
        $specificPrice = \CoreShop\Model\Product\PriceRule::getById($id);

        if ($specificPrice instanceof \CoreShop\Model\Product\PriceRule) {
            $this->_helper->json(array('success' => true, 'data' => $specificPrice->getObjectVars()));
        } else {
            $this->_helper->json(array('success' => false));
        }
    }

    public function saveAction()
    {
        $id = $this->getParam('id');
        $data = $this->getParam('data');
        $priceRule = \CoreShop\Model\Product\PriceRule::getById($id);

        if ($data && $priceRule instanceof \CoreShop\Model\Product\PriceRule) {
            $data = \Zend_Json::decode($this->getParam('data'));

            $conditions = $data['conditions'];
            $actions = $data['actions'];

            $conditionNamespace = 'CoreShop\\Model\\PriceRule\\Condition\\';
            $actionNamespace = 'CoreShop\\Model\\PriceRule\\Action\\';

            $conditionInstances = $priceRule->prepareConditions($conditions, $conditionNamespace);
            $actionInstances = $priceRule->prepareActions($actions, $actionNamespace);

            $priceRule->setValues($data['settings']);
            $priceRule->setActions($actionInstances);
            $priceRule->setConditions($conditionInstances);
            $priceRule->save();

            \Pimcore\Cache::clearTag('coreshop_product_price');

            $this->_helper->json(array('success' => true, 'data' => $priceRule));
        } else {
            $this->_helper->json(array('success' => false));
        }
    }

    public function deleteAction()
    {
        $id = $this->getParam('id');
        $priceRule = \CoreShop\Model\Product\PriceRule::getById($id);

        if ($priceRule instanceof \CoreShop\Model\Product\PriceRule) {
            $priceRule->delete();

            $this->_helper->json(array('success' => true));
        }

        $this->_helper->json(array('success' => false));
    }
}
