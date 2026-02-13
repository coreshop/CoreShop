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

namespace CoreShop\Bundle\NotificationBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\RuleFormSchemaCollector;
use CoreShop\Component\Notification\Model\NotificationRuleInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NotificationRuleController extends ResourceController
{
    public function getConfigAction(Request $request, RuleFormSchemaCollector $schemaCollector): Response
    {
        $conditions = [];
        $actions = [];
        $schemas = [];
        $types = [];

        /**
         * @var array $actionTypes
         */
        $actionTypes = $this->getParameter('coreshop.notification_rule.actions.types');

        /**
         * @var array $conditionTypes
         */
        $conditionTypes = $this->getParameter('coreshop.notification_rule.conditions.types');

        foreach ($actionTypes as $type) {
            if (!in_array($type, $types)) {
                $types[] = $type;
            }
        }

        foreach ($conditionTypes as $type) {
            if (!in_array($type, $types)) {
                $types[] = $type;
            }
        }

        $parameterBag = $this->container->get('parameter_bag');

        foreach ($types as $type) {
            $actionParameter = 'coreshop.notification_rule.actions.' . $type;
            $conditionParameter = 'coreshop.notification_rule.conditions.' . $type;

            if ($parameterBag->has($actionParameter)) {
                if (!array_key_exists($type, $actions)) {
                    $actions[$type] = [];
                }

                $actions[$type] = array_merge($actions[$type], array_keys($this->getParameter($actionParameter)));
            }

            if ($parameterBag->has($conditionParameter)) {
                if (!array_key_exists($type, $conditions)) {
                    $conditions[$type] = [];
                }

                $conditions[$type] = array_merge($conditions[$type], array_keys($this->getParameter($conditionParameter)));
            }

            // Collect schemas for this notification type
            $actionFormTypesParam = $actionParameter . '.form_types';
            $conditionFormTypesParam = $conditionParameter . '.form_types';

            if ($parameterBag->has($actionFormTypesParam)) {
                $schemas = array_merge($schemas, $schemaCollector->collectSchemas($this->getParameter($actionFormTypesParam)));
            }

            if ($parameterBag->has($conditionFormTypesParam)) {
                $schemas = array_merge($schemas, $schemaCollector->collectSchemas($this->getParameter($conditionFormTypesParam)));
            }
        }

        return $this->viewHandler->handle([
            'success' => true,
            'types' => $types,
            'actions' => $actions,
            'conditions' => $conditions,
            'schemas' => $schemas,
        ]);
    }

    public function sortAction(Request $request): Response
    {
        /**
         * @var EntityRepository&RepositoryInterface $repository
         */
        $repository = $this->repository;
        $rule = $this->getParameterFromRequest($request, 'rule');
        $toRule = $this->getParameterFromRequest($request, 'toRule');
        $position = $this->getParameterFromRequest($request, 'position');

        /**
         * @var NotificationRuleInterface $rule
         */
        $rule = $this->repository->find($rule);
        /**
         * @var NotificationRuleInterface $toRule
         */
        $toRule = $this->repository->find($toRule);

        $direction = $rule->getSort() < $toRule->getSort() ? 'down' : 'up';

        if ($direction === 'down') {
            //Update all records in between and move one direction up.

            $fromSort = $rule->getSort() + 1;
            $toSort = $toRule->getSort();

            if ($position === 'before') {
                --$toSort;
            }

            $criteria = new Criteria();
            $criteria->where($criteria->expr()->gte('sort', $fromSort));
            $criteria->where($criteria->expr()->lte('sort', $toSort));

            $result = $repository->matching($criteria);

            foreach ($result as $newRule) {
                if ($newRule instanceof NotificationRuleInterface) {
                    $newRule->setSort($newRule->getSort() - 1);

                    $this->manager->persist($newRule);
                }
            }

            $rule->setSort($toSort);

            $this->manager->persist($rule);
        } else {
            //Update all records in between and move one direction down.

            $fromSort = $toRule->getSort();
            $toSort = $rule->getSort();

            $criteria = new Criteria();
            $criteria->where($criteria->expr()->gte('sort', $fromSort));
            $criteria->where($criteria->expr()->lte('sort', $toSort));

            $result = $repository->matching($criteria);

            foreach ($result as $newRule) {
                if ($newRule instanceof NotificationRuleInterface) {
                    $newRule->setSort($newRule->getSort() + 1);

                    $this->manager->persist($newRule);
                }
            }

            $rule->setSort($fromSort);

            $this->manager->persist($rule);
        }

        $this->manager->flush();

        return $this->viewHandler->handle([
            'success' => true,
        ]);
    }
}
