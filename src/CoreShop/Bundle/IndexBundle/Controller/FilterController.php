<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\IndexBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Component\Index\Factory\ListingFactoryInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Registry\ServiceRegistry;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FilterController extends ResourceController
{
    public function getConfigAction(): Response
    {
        return $this->viewHandler->handle(
            [
                'success' => true,
                'pre_conditions' => array_keys($this->getPreConditionTypes()),
                'user_conditions' => array_keys($this->getUserConditionTypes()),
            ],
        );
    }

    public function getFieldsForIndexAction(Request $request, RepositoryInterface $indexRepository): Response
    {
        $index = $indexRepository->find($this->getParameterFromRequest($request, 'index'));

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

    public function getValuesForFilterFieldAction(Request $request, RepositoryInterface $indexRepository, ServiceRegistry $indexWorkersRegistry, ListingFactoryInterface $listingFactory): Response
    {
        $index = $indexRepository->find($this->getParameterFromRequest($request, 'index'));

        if ($index instanceof IndexInterface) {
            /**
             * @var WorkerInterface $worker
             */
            $worker = $indexWorkersRegistry->get($index->getWorker());
            $list = $listingFactory->createList($index);
            $list->setLocale($request->getLocale());
            $filterGroupHelper = $worker->getFilterGroupHelper();
            $field = $this->getParameterFromRequest($request, 'field');
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
     * @return array<string, string>
     */
    protected function getPreConditionTypes(): array
    {
        return $this->getParameter('coreshop.filter.pre_condition_types');
    }

    /**
     * @return array<string, string>
     */
    protected function getUserConditionTypes(): array
    {
        return $this->getParameter('coreshop.filter.user_condition_types');
    }
}
