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

namespace CoreShop\Bundle\OrderBundle\Workflow;

use CoreShop\Component\Order\Workflow\WorkflowManagerInterface;
use CoreShop\Component\Registry\ServiceRegistry;
use Pimcore\Event\Model\WorkflowEvent;

class WorkflowHelper
{
    public static function beforeDispatchOrderChange(WorkflowEvent $event)
    {
        $pimcoreManager = $event->getWorkflowManager();

        /**
         * Find WorkflowManager for Type.
         *
         * @var ServiceRegistry
         * @var $workflowManager WorkflowManagerInterface
         */
        $managerRegistry = self::get('coreshop.workflow.manager.registry');
        $workflowManager = $managerRegistry->get(get_class($pimcoreManager->getElement()));

        $data = $event->getArgument('data');
        $currentState = $pimcoreManager->getWorkflowStateForElement()->getState();
        $newState = $data['newState'];

        $workflowManager->beforeWorkflowDispatch($event->getWorkflowManager()->getElement(), $newState, $currentState);
    }

    public static function successDispatchOrderChange(WorkflowEvent $event)
    {
        $pimcoreManager = $event->getWorkflowManager();

        /**
         * Find WorkflowManager for Type.
         *
         * @var ServiceRegistry
         * @var $workflowManager WorkflowManagerInterface
         */
        $managerRegistry = self::get('coreshop.workflow.manager.registry');
        $workflowManager = $managerRegistry->get(get_class($pimcoreManager->getElement()));

        $data = $event->getArgument('data');
        $oldState = $data['oldState'];
        $newState = $data['newState'];

        $workflowManager->successWorkflowDispatch($event->getWorkflowManager()->getElement(), $newState, $oldState);
    }

    public static function failureDispatchOrderChange(WorkflowEvent $event)
    {
        $pimcoreManager = $event->getWorkflowManager();

        /**
         * Find WorkflowManager for Type.
         *
         * @var ServiceRegistry
         * @var $workflowManager WorkflowManagerInterface
         */
        $managerRegistry = self::get('coreshop.workflow.manager.registry');
        $workflowManager = $managerRegistry->get(get_class($pimcoreManager->getElement()));

        $data = $event->getArgument('data');
        $oldState = $data['oldState'];
        $newState = $data['newState'];

        $workflowManager->successWorkflowDispatch($event->getWorkflowManager()->getElement(), $newState, $oldState);
    }

    private static function get($id)
    {
        return \Pimcore::getContainer()->get($id);
    }
}
