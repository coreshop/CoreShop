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

use CoreShop\Model\Product\Filter;
use CoreShop\Controller\Action\Admin;

/**
 * Class CoreShop_Admin_FilterController
 */
class CoreShop_Admin_FilterController extends Admin
{
    public function init()
    {
        parent::init();

        // check permissions
        $notRestrictedActions = array('list');

        if (!in_array($this->getParam('action'), $notRestrictedActions)) {
            $this->checkPermission('coreshop_permission_filters');
        }
    }

    public function listAction()
    {
        $list = Filter::getList();

        $data = array();
        if (is_array($list->getData())) {
            foreach ($list->getData() as $group) {
                $data[] = $this->getTreeNodeConfig($group);
            }
        }
        $this->_helper->json($data);
    }

    protected function getTreeNodeConfig(Filter $filter)
    {
        $tmp = array(
            'id' => $filter->getId(),
            'text' => $filter->getName(),
            'qtipCfg' => array(
                'title' => 'ID: '.$filter->getId(),
            ),
            'name' => $filter->getName(),
        );
        
        return $tmp;
    }

    public function addAction()
    {
        $name = $this->getParam('name');

        if (strlen($name) <= 0) {
            $this->helper->json(array('success' => false, 'message' => $this->getTranslator()->translate('Name must be set')));
        } else {
            $filter = new Filter();
            $filter->setName($name);
            $filter->setResultsPerPage(20);
            $filter->setOrder('desc');
            $filter->setOrderKey('name');
            $filter->setPreConditions(array());
            $filter->setFilters(array());
            $filter->setSimilarities(array());
            $filter->save();

            $this->_helper->json(array('success' => true, 'data' => $filter));
        }
    }

    public function getAction()
    {
        $id = $this->getParam('id');
        $filter = Filter::getById($id);

        if ($filter instanceof Filter) {
            $data = get_object_vars($filter);
            $data['index'] = $filter->getIndex() instanceof \CoreShop\Model\Index ? $filter->getIndex()->getId() : null;

            $this->_helper->json(array('success' => true, 'data' => $data));
        } else {
            $this->_helper->json(array('success' => false));
        }
    }

    public function saveAction()
    {
        $id = $this->getParam('id');
        $data = $this->getParam('data');
        $filter = Filter::getById($id);

        if ($data && $filter instanceof Filter) {
            $data = \Zend_Json::decode($this->getParam('data'));

            $preConditions = $data['conditions'];
            $preConditionInstances = array();

            $conditionNamespace = 'CoreShop\\Model\\Product\\Filter\\Condition\\';
            $similarityNamespace = 'CoreShop\\Model\\Product\\Filter\\Similarity\\';

            foreach ($preConditions as $condition) {
                $class = $conditionNamespace.ucfirst($condition['type']);

                if (\Pimcore\Tool::classExists($class)) {
                    $instance = new $class();
                    $instance->setValues($condition);

                    $preConditionInstances[] = $instance;
                } else {
                    throw new \CoreShop\Exception(sprintf('Condition with type %s not found'), $condition['type']);
                }
            }

            $filters = $data['filters'];
            $filtersInstances = array();

            foreach ($filters as $filterCondition) {
                $class = $conditionNamespace.ucfirst($filterCondition['type']);

                if (\Pimcore\Tool::classExists($class)) {
                    $instance = new $class();
                    $instance->setValues($filterCondition);

                    $filtersInstances[] = $instance;
                } else {
                    throw new \CoreShop\Exception(sprintf('Condition with type %s not found'), $filterCondition['type']);
                }
            }

            $similarities = $data['similarities'];
            $similaritiesInstances = array();

            foreach ($similarities as $similarity) {
                $class = $similarityNamespace.ucfirst($similarity['type']);

                if (\Pimcore\Tool::classExists($class)) {
                    $instance = new $class();
                    $instance->setValues($similarity);

                    $similaritiesInstances[] = $instance;
                } else {
                    throw new \CoreShop\Exception(sprintf('Condition with type %s not found', $similarity['type']));
                }
            }

            $filter->setValues($data['settings']);
            $filter->setPreConditions($preConditionInstances);
            $filter->setFilters($filtersInstances);
            $filter->setSimilarities($similaritiesInstances);
            $filter->save();

            $this->_helper->json(array('success' => true, 'data' => $filter));
        } else {
            $this->_helper->json(array('success' => false));
        }
    }

    public function deleteAction()
    {
        $id = $this->getParam('id');
        $filter = Filter::getById($id);

        if ($filter instanceof Filter) {
            $filter->delete();

            $this->_helper->json(array('success' => true));
        }

        $this->_helper->json(array('success' => false));
    }

    public function getConfigAction()
    {
        $this->_helper->json(array(
            'success' => true,
            'conditions' => Filter::getConditions(),
            'similarities' => Filter::getSimilarityTypes()
        ));
    }

    public function getFieldsForIndexAction()
    {
        $index = \CoreShop\Model\Index::getById($this->getParam('index'));

        if ($index instanceof \CoreShop\Model\Index) {
            $columns = array();
            $config = $index->getConfig();

            if ($config->columns) {
                foreach ($config->columns as $col) {
                    $columns[] = array(
                        'name' => $col->name,
                    );
                }
            }

            $this->_helper->json($columns);
        }

        $this->_helper->json(false);
    }

    public function getValuesForFilterFieldAction()
    {
        $index = \CoreShop\Model\Index::getById($this->getParam('index'));

        if ($index instanceof \CoreShop\Model\Index) {
            $list = \CoreShop\IndexService::getIndexService()->getWorker($index->getName());
            $productList = $list->getProductList();

            $values = $productList->getGroupByValues($this->getParam('field'));
            $returnValues = array();

            foreach ($values as $value) {
                if ($value) {
                    $returnValues[] = array(
                        'value' => $value,
                        'key' => $value,
                    );
                } else {
                    $returnValues[] = array(
                        'value' => Filter\Service::EMPTY_STRING,
                        'key' => 'empty',
                    );
                }
            }

            $this->_helper->json($returnValues);
        }

        $this->_helper->json(false);
    }
}
