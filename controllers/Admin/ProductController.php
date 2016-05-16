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
use Pimcore\Controller\Action\Admin;
use Pimcore\Tool as PimTool;

class CoreShop_Admin_ProductController extends Admin
{
    public function init()
    {
        parent::init();

        // check permissions
        $notRestrictedActions = array('list');
        if (!in_array($this->getParam('action'), $notRestrictedActions)) {
            //$this->checkPermission("coreshop_permission_priceRules");
            //TODO
        }
    }

    public function getProductsAction()
    {
        $list = \CoreShop\Model\Product::getList();
        $list->setLimit($this->getParam('limit', 30));
        $list->setOffset($this->getParam('page', 1) - 1);

        if ($this->getParam('filter', null)) {
            $conditionFilters[] = \Pimcore\Model\Object\Service::getFilterCondition($this->getParam('filter'), \Pimcore\Model\Object\ClassDefinition::getByName('CoreShopProduct'));
            if (count($conditionFilters) > 0 && $conditionFilters[0] !== '(())') {
                $list->setCondition(implode(' AND ', $conditionFilters));
            }
        }

        $sortingSettings = \Pimcore\Admin\Helper\QueryParams::extractSortingSettings($this->getAllParams());

        $order = 'DESC';
        $orderKey = 'o_id';

        if ($sortingSettings['order']) {
            $order = $sortingSettings['order'];
        }
        if (strlen($sortingSettings['orderKey']) > 0) {
            $orderKey = $sortingSettings['orderKey'];
        }

        $list->setOrder($order);
        $list->setOrderKey($orderKey);

        $products = $list->load();
        $jsonProducts = array();

        foreach ($products as $product) {
            $jsonProducts[] = $this->prepareProduct($product);
        }

        $this->_helper->json(array('success' => true, 'data' => $jsonProducts, 'count' => count($jsonProducts), 'total' => $list->getTotalCount()));
    }

    protected function prepareProduct(\CoreShop\Model\Product $product)
    {
        $element = array(
            'o_id' => $product->getId(),
            'name' => $product->getName(),
            'quantity' => $product->getQuantity(),
            'price' => $product->getPrice(),
        );

        return $element;
    }

    public function listSpecificPricesAction()
    {
        $product = \CoreShop\Model\Product::getById($this->getParam('product'));

        if ($product instanceof \CoreShop\Model\Product) {
            $prices = $product->getSpecificPrices();
            $data = array();

            foreach ($prices as $price) {
                $data[] = $this->getSpecificPriceTreeNodeConfig($price);
            }

            $this->_helper->json(array('success' => true, 'data' => $data));
        } else {
            $this->_helper->json(array('success' => false));
        }
    }

    protected function getSpecificPriceTreeNodeConfig($price)
    {
        $tmpPriceRule = array(
            'id' => $price->getId(),
            'text' => $price->getName(),
            'elementType' => 'specificprice',
            'qtipCfg' => array(
                'title' => 'ID: '.$price->getId(),
            ),
            'name' => $price->getName(),
        );

        $tmpPriceRule['leaf'] = true;
        $tmpPriceRule['iconCls'] = 'coreshop_icon_price_rule'; //TODO: change icon
        $tmpPriceRule['allowChildren'] = false;

        return $tmpPriceRule;
    }

    public function getSpecificPriceConfigAction()
    {
        $this->_helper->json(array(
            'success' => true,
            'conditions' => \CoreShop\Model\Product\SpecificPrice::$availableConditions,
            'actions' => \CoreShop\Model\Product\SpecificPrice::$availableActions,
        ));
    }

    public function addSpecificPriceAction()
    {
        $product = \CoreShop\Model\Product::getById($this->getParam('product'));
        $name = $this->getParam('name');

        if (strlen($name) <= 0 && $product instanceof \CoreShop\Model\Product) {
            $this->helper->json(array('success' => false, 'message' => $this->getTranslator()->translate('Name must be set')));
        } else {
            $specificPrice = new \CoreShop\Model\Product\SpecificPrice();
            $specificPrice->setName($name);
            $specificPrice->setO_Id($product->getId());
            $specificPrice->save();

            $this->_helper->json(array('success' => true, 'data' => $specificPrice));
        }
    }

    public function getSpecificPriceAction()
    {
        $id = $this->getParam('id');
        $specificPrice = \CoreShop\Model\Product\SpecificPrice::getById($id);

        if ($specificPrice instanceof \CoreShop\Model\Product\SpecificPrice) {
            $this->_helper->json(array('success' => true, 'data' => $specificPrice->getObjectVars()));
        } else {
            $this->_helper->json(array('success' => false));
        }
    }

    public function saveSpecificPriceAction()
    {
        $id = $this->getParam('id');
        $data = $this->getParam('data');
        $specificPrice = \CoreShop\Model\Product\SpecificPrice::getById($id);

        if ($data && $specificPrice instanceof \CoreShop\Model\Product\SpecificPrice) {
            $data = \Zend_Json::decode($this->getParam('data'));

            $conditions = $data['conditions'];
            $actions = $data['actions'];
            $actionInstances = array();
            $conditionInstances = array();

            $actionNamespace = 'CoreShop\\Model\\Product\\SpecificPrice\\Action\\';
            $conditionNamespace = 'CoreShop\\Model\\Product\\SpecificPrice\\Condition\\';

            foreach ($conditions as $condition) {
                $class = $conditionNamespace.ucfirst($condition['type']);

                if (PimTool::classExists($class)) {
                    $instance = new $class();
                    $instance->setValues($condition);

                    $conditionInstances[] = $instance;
                } else {
                    throw new \Exception(sprintf('Condition with type %s not found'), $condition['type']);
                }
            }

            foreach ($actions as $action) {
                $class = $actionNamespace.ucfirst($action['type']);

                if (PimTool::classExists($class)) {
                    $instance = new $class();
                    $instance->setValues($action);

                    $actionInstances[] = $instance;
                } else {
                    throw new \Exception(sprintf('Action with type %s not found'), $action['type']);
                }
            }

            $specificPrice->setValues($data['settings']);
            $specificPrice->setActions($actionInstances);
            $specificPrice->setConditions($conditionInstances);
            $specificPrice->save();

            \Pimcore\Cache::clearTag('coreshop_product_'.$specificPrice->getO_Id().'_price');

            $this->_helper->json(array('success' => true, 'data' => $specificPrice));
        } else {
            $this->_helper->json(array('success' => false));
        }
    }

    public function deleteSpecificPriceAction()
    {
        $id = $this->getParam('id');
        $specificPrice = \CoreShop\Model\Product\SpecificPrice::getById($id);

        if ($specificPrice instanceof \CoreShop\Model\Product\SpecificPrice) {
            $specificPrice->delete();

            $this->_helper->json(array('success' => true));
        }

        $this->_helper->json(array('success' => false));
    }
}
