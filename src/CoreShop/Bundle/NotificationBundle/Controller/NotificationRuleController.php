<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\NotificationBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Component\Notification\Model\NotificationRuleInterface;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;

class NotificationRuleController extends ResourceController
{
    public function getConfigAction(Request $request)
    {
        $conditions = [];
        $actions = [];
        $types = [];

        $actionTypes = $this->getParameter('coreshop.notification_rule.actions.types');
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

        foreach ($types as $type) {
            $actionParameter = 'coreshop.notification_rule.actions.' . $type;
            $conditionParameter = 'coreshop.notification_rule.conditions.' . $type;

            if ($this->container->hasParameter($actionParameter)) {
                if (!array_key_exists($type, $actions)) {
                    $actions[$type] = [];
                }

                $actions[$type] = array_merge($actions[$type], array_keys($this->getParameter($actionParameter)));
            }

            if ($this->container->hasParameter($conditionParameter)) {
                if (!array_key_exists($type, $conditions)) {
                    $conditions[$type] = [];
                }

                $conditions[$type] = array_merge($conditions[$type], array_keys($this->getParameter($conditionParameter)));
            }
        }

        return $this->viewHandler->handle([
            'success' => true,
            'types' => $types,
            'actions' => $actions,
            'conditions' => $conditions,
        ]);
    }

    public function sortAction(Request $request)
    {
        /**
         * @var EntityRepository $repository
         */
        $repository = $this->repository;
        $rule = $request->get('rule');
        $toRule = $request->get('toRule');
        $position = $request->get('position');

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
                $toSort--;
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
