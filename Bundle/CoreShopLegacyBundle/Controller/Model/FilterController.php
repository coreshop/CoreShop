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

namespace CoreShop\Bundle\CoreShopLegacyBundle\Controller\Model;

use CoreShop\Bundle\CoreShopLegacyBundle\Controller\Admin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FilterController
 *
 * @Route("/filter")
 */
class FilterController extends Admin\DataController
{
    /**
     * @var string
     */
    protected $permission = 'coreshop_permission_filters';

    /**
     * @var string
     */
    protected $model = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Product\Filter::class;

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model
     */
    protected function setDefaultValues(\CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model)
    {
        if($model instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Product\Filter) {
            $model->setResultsPerPage(20);
            $model->setOrder('desc');
            $model->setOrderKey('name');
            $model->setPreConditions([]);
            $model->setFilters([]);
            $model->setSimilarities([]);
        }
    }

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model
     * @return array
     */
    protected function getReturnValues(\CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model)
    {
        if($model instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Product\Filter) {
            $json = $model->serialize();

            $json['index'] = $model->getIndex() instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Index ? $model->getIndex()->getId() : null;

            return $json;
        }

        return parent::getReturnValues($model);
    }

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model
     * @param $data
     */
    protected function prepareSave(\CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model, $data) {
        if($model instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Product\Filter) {
            $filtersInstances = $model->prepareConditions($data['filters']);
            $preConditionInstances = $model->prepareConditions($data['conditions']);
            $similaritiesInstances = $model->prepareSimilarities($data['similarities']);

            $model->setValues($data['settings']);
            $model->setPreConditions($preConditionInstances);
            $model->setFilters($filtersInstances);
            $model->setSimilarities($similaritiesInstances);
        }
    }

    /**
     * @return \Pimcore\Bundle\PimcoreAdminBundle\HttpFoundation\JsonResponse
     *
     * @Route("/get-config")
     */
    public function getConfigAction()
    {
        return $this->json([
            'success' => true,
            'conditions' => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Product\Filter::getConditionDispatcher()->getTypeKeys(),
            'similarities' => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Product\Filter::getSimilaritiesDispatcher()->getTypeKeys()
        ]);
    }

    /**
     * @param Request $request
     * @return \Pimcore\Bundle\PimcoreAdminBundle\HttpFoundation\JsonResponse
     *
     * @Route("/get-fields-for-index")
     */
    public function getFieldsForIndexAction(Request $request)
    {
        $index = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Index::getById($request->get('index'));

        if ($index instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Index) {
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

            return $this->json($columns);
        }

        return $this->json(false);
    }

    /**
     * @param Request $request
     * @return \Pimcore\Bundle\PimcoreAdminBundle\HttpFoundation\JsonResponse
     *
     * @Route("/get-values-for-filter-field")
     */
    public function getValuesForFilterFieldAction(Request $request)
    {
        $index = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Index::getById($request->get('index'));

        if ($index instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Index) {
            $list = \CoreShop\Bundle\CoreShopLegacyBundle\IndexService::getIndexService()->getWorker($index->getName());
            $productList = $list->getProductList();

            $values = $productList->getGroupByValues($request->get('field'));
            $returnValues = [];

            foreach ($values as $value) {
                if ($value) {
                    $returnValues[] = [
                        'value' => $value,
                        'key' => $value,
                    ];
                } else {
                    $returnValues[] = [
                        'value' => \CoreShop\Bundle\CoreShopLegacyBundle\Model\Product\Filter\Service::EMPTY_STRING,
                        'key' => 'empty',
                    ];
                }
            }

            return $this->json($returnValues);
        }

        return $this->json(false);
    }
}
