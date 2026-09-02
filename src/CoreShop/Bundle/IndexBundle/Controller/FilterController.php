<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\IndexBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\RuleFormSchemaCollector;
use CoreShop\Component\Index\Factory\ListingFactoryInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Registry\ServiceRegistry;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FilterController extends ResourceController
{
    public function getConfigAction(
        #[Autowire(service: 'coreshop.form_registry.filter.pre_condition_types')]
        FormTypeRegistryInterface $preConditionFormRegistry,
        #[Autowire(service: 'coreshop.form_registry.filter.user_condition_types')]
        FormTypeRegistryInterface $userConditionFormRegistry,
        // Provided by CoreShopStudioFormBundle, which is only registered when
        // Pimcore Studio is installed — null on classic-admin-only setups.
        ?RuleFormSchemaCollector $schemaCollector = null,
    ): Response {
        $preConditionTypes = array_keys($this->getPreConditionTypes());
        $userConditionTypes = array_keys($this->getUserConditionTypes());

        $payload = [
            'success' => true,
            'pre_conditions' => $preConditionTypes,
            'user_conditions' => $userConditionTypes,
        ];

        if (null !== $schemaCollector) {
            $preConditionSchemas = $schemaCollector->collectSchemasWithTypeMap($preConditionFormRegistry, $preConditionTypes);
            $userConditionSchemas = $schemaCollector->collectSchemasWithTypeMap($userConditionFormRegistry, $userConditionTypes);

            $payload['schemas'] = array_merge(
                $preConditionSchemas['schemas'],
                $userConditionSchemas['schemas'],
            );
            $payload['preConditionSchemaByType'] = $preConditionSchemas['schemaByType'];
            $payload['userConditionSchemaByType'] = $userConditionSchemas['schemaByType'];
        }

        return $this->viewHandler->handle($payload);
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
