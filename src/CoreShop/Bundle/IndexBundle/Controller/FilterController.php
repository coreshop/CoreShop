<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\IndexBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use Symfony\Component\HttpFoundation\Request;

class FilterController extends ResourceController
{
    /**
     * Get Index Configurations.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getConfigAction()
    {
        return $this->viewHandler->handle(
            [
                'success' => true,
                'conditions' => array_keys($this->getConditionTypes()),
            ]
        );
    }

    public function getFieldsForIndexAction(Request $request)
    {
        $index = $this->get('coreshop.repository.index')->find($request->get('index'));

        if ($index instanceof IndexInterface) {
            $columns = [
            ];

            foreach ($index->getColumns() as $col) {
                $columns[] = [
                    'name' => $col->getName(),
                ];
            }

            return $this->viewHandler->handle($columns);
        }

        return $this->viewHandler->handle(false);
    }

    public function getValuesForFilterFieldAction(Request $request)
    {
        $index = $this->get('coreshop.repository.index')->find($request->get('index'));

        if ($index instanceof IndexInterface) {
            /**
             * @var WorkerInterface $worker
             */
            $worker = $this->get('coreshop.registry.index.worker')->get($index->getWorker());
            $list = $this->get('coreshop.factory.index.list')->createList($index);
            $filterGroupHelper = $worker->getFilterGroupHelper();
            $field = $request->get('field');
            $column = null;

            foreach ($index->getColumns() as $column) {
                if ($column->getName() === $field) {
                    break;
                }
            }
            $returnValues = [];

            if ($column instanceof IndexColumnInterface) {
                $returnValues = $filterGroupHelper->getGroupByValuesForFilterGroup($column, $list, $field);
            }

            return $this->viewHandler->handle($returnValues);
        }

        return $this->viewHandler->handle(false);
    }

    /**
     * @return array
     */
    protected function getConditionTypes()
    {
        return $this->getParameter('coreshop.filter.condition_types');
    }
}
