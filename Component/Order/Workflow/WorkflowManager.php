<?php

namespace CoreShop\Component\Order\Workflow;

use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

class WorkflowManager implements WorkflowManagerInterface
{
    /**
     * @var PrioritizedServiceRegistryInterface
     */
    private $serviceRegistry;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var string
     */
    private $class;

    /**
     * @param PrioritizedServiceRegistryInterface $serviceRegistry
     * @param EventDispatcherInterface $eventDispatcher
     * @param string $class
     */
    public function __construct(PrioritizedServiceRegistryInterface $serviceRegistry, EventDispatcherInterface $eventDispatcher, $class)
    {
        $this->serviceRegistry = $serviceRegistry;
        $this->eventDispatcher = $eventDispatcher;
        $this->class = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function addValidator(ProposalValidatorInterface $proposalValidator, $priority)
    {
        $this->serviceRegistry->register(get_class($proposalValidator), $priority, $proposalValidator);
    }

    /**
     * {@inheritdoc}
     */
    public function validateNewState(ProposalInterface $proposal, $currentState, $newState)
    {
        /**
         * @var $validator ProposalValidatorInterface
         */
        foreach ($this->serviceRegistry->all() as $validator) {
            if (!$validator->isValidForState($proposal, $currentState, $newState)) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeWorkflowDispatch(ProposalInterface $proposal, $newState, $currentState)
    {
        Assert::isInstanceOf($proposal, $this->class);

        if ($proposal instanceof ProposalInterface) {
            if ($currentState === $newState) {
                throw new \Exception('Cannot apply same orderState again. (' . $currentState . ' => ' . $newState . ')');
            } else if (!$this->validateNewState($proposal, $currentState, $newState)) {
                throw new \Exception('New State is not valid.');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function successWorkflowDispatch(ProposalInterface $proposal, $newState, $oldState)
    {
        Assert::isInstanceOf($proposal, $this->class);

        $this->eventDispatcher->dispatch('coreshop.workflow_manager.success', new ProposalWorkflowEvent($proposal, $newState, $oldState));
    }

    /**
     * {@inheritdoc}
     */
    public function failureWorkflowDispatch(ProposalInterface $proposal, $newState, $oldState)
    {
        Assert::isInstanceOf($proposal, $this->class);

        $this->eventDispatcher->dispatch('coreshop.workflow_manager.failure', new ProposalWorkflowEvent($proposal, $newState, $oldState));
    }
}