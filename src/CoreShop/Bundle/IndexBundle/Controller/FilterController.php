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

declare(strict_types=1);

namespace CoreShop\Bundle\IndexBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Bundle\ResourceBundle\Controller\ViewHandlerInterface;
use CoreShop\Component\Index\Factory\ListingFactoryInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FilterController extends ResourceController
{

    public function getConfigAction(ViewHandlerInterface $viewHandler): Response
    {
        return $viewHandler->handle(
            [
                'success' => true,
                'pre_conditions' => array_keys($this->getPreConditionTypes()),
                'user_conditions' => array_keys($this->getUserConditionTypes()),
            ]
        );
    }

    public function getFieldsForIndexAction(
        Request $request,
        RepositoryInterface $indexRepository,
        ViewHandlerInterface $viewHandler
    ): Response
    {
        $index = $indexRepository->find($request->get('index'));

        if ($index instanceof IndexInterface) {
            $columns = [
            ];

            foreach ($index->getColumns() as $col) {
                $columns[] = [
                    'name' => $col->getName(),
                ];
            }

            return $viewHandler->handle($columns);
        }

        return $viewHandler->handle(false);
    }

    public function getValuesForFilterFieldAction(
        Request $request,
        RepositoryInterface $indexRepository,
        ServiceRegistryInterface $indexWorkerRegistry,
        ListingFactoryInterface $listingFactory,
        ViewHandlerInterface $viewHandler
    ): Response
    {
        $index = $indexRepository->find($request->get('index'));

        if ($index instanceof IndexInterface) {
            /**
             * @var WorkerInterface $worker
             */
            $worker = $indexWorkerRegistry->get($index->getWorker());
            $list = $listingFactory->createList($index);
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

            return $viewHandler->handle($returnValues);
        }

        return $viewHandler->handle(false);
    }

    /**
     * @return array
     */
    protected function getPreConditionTypes(): array
    {
        return $this->getParameter('coreshop.filter.pre_condition_types');
    }

    /**
     * @return array
     */
    protected function getUserConditionTypes(): array
    {
        return $this->getParameter('coreshop.filter.user_condition_types');
    }
}
