<?php

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
         * Find WorkflowManager for Type
         *
         * @var $serviceRegistry ServiceRegistry
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
         * Find WorkflowManager for Type
         *
         * @var $serviceRegistry ServiceRegistry
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
         * Find WorkflowManager for Type
         *
         * @var $serviceRegistry ServiceRegistry
         * @var $workflowManager WorkflowManagerInterface
         */
        $managerRegistry = self::get('coreshop.workflow.manager.registry');
        $workflowManager = $managerRegistry->get(get_class($pimcoreManager->getElement()));


        $data = $event->getArgument('data');
        $oldState = $data['oldState'];
        $newState = $data['newState'];

        $workflowManager->successWorkflowDispatch($event->getWorkflowManager()->getElement(), $newState, $oldState);
    }

    private static function get($id) {
        return \Pimcore::getContainer()->get($id);
    }
}