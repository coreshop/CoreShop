<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\IndexBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Component\Index\Factory\ListingFactoryInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
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
            ]
        );
    }

    public function getFieldsForIndexAction(Request $request): Response
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

    public function getValuesForFilterFieldAction(Request $request): Response
    {
        $index = $this->get('coreshop.repository.index')->find($request->get('index'));

        if ($index instanceof IndexInterface) {
            /**
             * @var WorkerInterface $worker
             */
            $worker = $this->get('coreshop.registry.index.worker')->get($index->getWorker());
            $list = $this->get(ListingFactoryInterface::class)->createList($index);
            $list->setLocale($request->getLocale());
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
    protected function getPreConditionTypes(): array
    {
        return $this->container->getParameter('coreshop.filter.pre_condition_types');
    }

    /**
     * @return array
     */
    protected function getUserConditionTypes(): array
    {
        return $this->container->getParameter('coreshop.filter.user_condition_types');
    }
}
