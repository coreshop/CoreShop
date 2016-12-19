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
        $notRestrictedActions = ['list'];

        if (!in_array($this->getParam('action'), $notRestrictedActions)) {
            $this->checkPermission('coreshop_permission_filters');
        }
    }

    public function listAction()
    {
        $list = Filter::getList();

        $data = [];
        if (is_array($list->getData())) {
            foreach ($list->getData() as $group) {
                $data[] = $this->getTreeNodeConfig($group);
            }
        }
        $this->_helper->json($data);
    }

    protected function getTreeNodeConfig(Filter $filter)
    {
        $tmp = [
            'id' => $filter->getId(),
            'text' => $filter->getName(),
            'qtipCfg' => [
                'title' => 'ID: '.$filter->getId(),
            ],
            'name' => $filter->getName(),
        ];
        
        return $tmp;
    }

    public function addAction()
    {
        $name = $this->getParam('name');

        if (strlen($name) <= 0) {
            $this->helper->json(['success' => false, 'message' => $this->getTranslator()->translate('Name must be set')]);
        } else {
            $filter = Filter::create();
            $filter->setName($name);
            $filter->setResultsPerPage(20);
            $filter->setOrder('desc');
            $filter->setOrderKey('name');
            $filter->setPreConditions([]);
            $filter->setFilters([]);
            $filter->setSimilarities([]);
            $filter->save();

            $this->_helper->json(['success' => true, 'data' => $filter]);
        }
    }

    public function getAction()
    {
        $id = $this->getParam('id');
        $filter = Filter::getById($id);

        if ($filter instanceof Filter) {
            $data = get_object_vars($filter);
            $data['index'] = $filter->getIndex() instanceof \CoreShop\Model\Index ? $filter->getIndex()->getId() : null;

            $this->_helper->json(['success' => true, 'data' => $data]);
        } else {
            $this->_helper->json(['success' => false]);
        }
    }

    public function saveAction()
    {
        $id = $this->getParam('id');
        $data = $this->getParam('data');
        $filter = Filter::getById($id);

        if ($data && $filter instanceof Filter) {
            $data = \Zend_Json::decode($this->getParam('data'));

            $conditionNamespace = 'CoreShop\\Model\\Product\\Filter\\Condition\\';
            $similarityNamespace = 'CoreShop\\Model\\Product\\Filter\\Similarity\\';

            $filtersInstances = $filter->prepareConditions($data['filters'], $conditionNamespace);
            $preConditionInstances = $filter->prepareConditions($data['conditions'], $conditionNamespace);
            $similaritiesInstances = $filter->prepareSimilarities($data['similarities'], $similarityNamespace);

            $filter->setValues($data['settings']);
            $filter->setPreConditions($preConditionInstances);
            $filter->setFilters($filtersInstances);
            $filter->setSimilarities($similaritiesInstances);
            $filter->save();

            $this->_helper->json(['success' => true, 'data' => $filter]);
        } else {
            $this->_helper->json(['success' => false]);
        }
    }

    public function deleteAction()
    {
        $id = $this->getParam('id');
        $filter = Filter::getById($id);

        if ($filter instanceof Filter) {
            $filter->delete();

            $this->_helper->json(['success' => true]);
        }

        $this->_helper->json(['success' => false]);
    }

    public function getConfigAction()
    {
        $this->_helper->json([
            'success' => true,
            'conditions' => Filter::getConditions(),
            'similarities' => Filter::getSimilarityTypes()
        ]);
    }

    public function getFieldsForIndexAction()
    {
        $index = \CoreShop\Model\Index::getById($this->getParam('index'));

        if ($index instanceof \CoreShop\Model\Index) {
            $columns = [
                ['name' => 'minPrice'],
                ['name' => 'maxPrice']
            ];
            $config = $index->getConfig();

            if ($config->columns) {
                foreach ($config->columns as $col) {
                    $columns[] = [
                        'name' => $col->name,
                    ];
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
            $returnValues = [];

            foreach ($values as $value) {
                if ($value) {
                    $returnValues[] = [
                        'value' => $value,
                        'key' => $value,
                    ];
                } else {
                    $returnValues[] = [
                        'value' => Filter\Service::EMPTY_STRING,
                        'key' => 'empty',
                    ];
                }
            }

            $this->_helper->json($returnValues);
        }

        $this->_helper->json(false);
    }
}
