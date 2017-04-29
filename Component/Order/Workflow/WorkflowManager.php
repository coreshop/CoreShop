<?php

namespace CoreShop\Component\Order\Workflow;

use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;
use Pimcore\Model\Element\Note;
use Symfony\Component\Translation\TranslatorInterface;
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
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $noteIdentifier;

    /**
     * @param PrioritizedServiceRegistryInterface $serviceRegistry
     * @param EventDispatcherInterface $eventDispatcher
     * @param string $class
     * @param TranslatorInterface $translator
     * @param string $noteIdentifier
     */
    public function __construct(
        PrioritizedServiceRegistryInterface $serviceRegistry,
        EventDispatcherInterface $eventDispatcher,
        $class,
        TranslatorInterface $translator,
        $noteIdentifier
    )
    {
        $this->serviceRegistry = $serviceRegistry;
        $this->eventDispatcher = $eventDispatcher;
        $this->class = $class;
        $this->translator = $translator;
        $this->noteIdentifier = $noteIdentifier;
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

    /**
     * {@inheritdoc}
     */
    public function getCurrentState(ProposalInterface $proposal)
    {
        Assert::isInstanceOf($proposal, $this->class);

        $user = \Pimcore\Model\User::getById(0);
        $manager = \Pimcore\WorkflowManagement\Workflow\Manager\Factory::getManager($proposal, $user);

        $state = $manager->getWorkflowStateForElement()->getState();
        $status = $manager->getWorkflowStateForElement()->getStatus();

        $info = [];

        if (!is_null($state)) {
            $info['state'] = $manager->getWorkflow()->getStateConfig($state);
            $info['state']['translatedLabel'] = $this->translator->trans($info['state']['label']);
            //$info['state']['color'] = self::$STATE_CONFIG[$state]['color'];
            $info['state']['color'] = '#00FF00'; //TODO: How to get the color? I mean, where to get it?
        }

        if (!is_null($status)) {
            $info['status'] = $manager->getWorkflow()->getStatusConfig($status);
            $info['status']['translatedLabel'] = $this->translator->trans($info['state']['label']);
        }

        return !empty($info) ? $info : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getStateHistory(ProposalInterface $proposal)
    {
        /* @var \Pimcore\Model\Element\Note\Listing $noteList */
        $noteList = new Note\Listing();
        $noteList->addConditionParam('type = ?', $this->noteIdentifier);
        $noteList->addConditionParam('cid = ?', $proposal->getId());
        $noteList->setOrderKey('date');
        $noteList->setOrder('desc');

        return $noteList->load();
    }
}