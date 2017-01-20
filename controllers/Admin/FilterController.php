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
 * Class CoreShop_Admin_FilterController
 */
class CoreShop_Admin_FilterController extends Admin\Data
{
    /**
     * @var string
     */
    protected $permission = 'coreshop_permission_filters';

    /**
     * @var string
     */
    protected $model = \CoreShop\Model\Product\Filter::class;

    /**
     * @param \CoreShop\Model\AbstractModel $model
     */
    protected function setDefaultValues(\CoreShop\Model\AbstractModel $model)
    {
        if($model instanceof \CoreShop\Model\Product\Filter) {
            $model->setResultsPerPage(20);
            $model->setOrder('desc');
            $model->setOrderKey('name');
            $model->setPreConditions([]);
            $model->setFilters([]);
            $model->setSimilarities([]);
        }
    }

    /**
     * @param \CoreShop\Model\AbstractModel $model
     * @return array
     */
    protected function getReturnValues(\CoreShop\Model\AbstractModel $model)
    {
        if($model instanceof \CoreShop\Model\Product\Filter) {
            $json = $model->serialize();

            $json['index'] = $model->getIndex() instanceof \CoreShop\Model\Index ? $model->getIndex()->getId() : null;

            return $json;
        }

        return parent::getReturnValues($model);
    }

    /**
     * @param \CoreShop\Model\AbstractModel $model
     * @param $data
     */
    protected function prepareSave(\CoreShop\Model\AbstractModel $model, $data) {
        if($model instanceof \CoreShop\Model\Product\Filter) {
            $filtersInstances = $model->prepareConditions($data['filters']);
            $preConditionInstances = $model->prepareConditions($data['conditions']);
            $similaritiesInstances = $model->prepareSimilarities($data['similarities']);

            $model->setValues($data['settings']);
            $model->setPreConditions($preConditionInstances);
            $model->setFilters($filtersInstances);
            $model->setSimilarities($similaritiesInstances);
        }
    }

    public function getConfigAction()
    {
        $this->_helper->json([
            'success' => true,
            'conditions' => \CoreShop\Model\Product\Filter::getConditionDispatcher()->getTypeKeys(),
            'similarities' => \CoreShop\Model\Product\Filter::getSimilaritiesDispatcher()->getTypeKeys()
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
                        'value' => \CoreShop\Model\Product\Filter\Service::EMPTY_STRING,
                        'key' => 'empty',
                    ];
                }
            }

            $this->_helper->json($returnValues);
        }

        $this->_helper->json(false);
    }
}
