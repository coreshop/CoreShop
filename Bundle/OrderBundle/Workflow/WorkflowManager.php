<?php

namespace CoreShop\Bundle\OrderBundle\Workflow;

use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Workflow\ProposalValidatorInterface;
use CoreShop\Component\Order\Workflow\ProposalWorkflowEvent;
use CoreShop\Component\Order\Workflow\WorkflowManagerInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;
use Pimcore\Bundle\AdminBundle\Security\User\User;
use Pimcore\Model\Element\Note;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

final class WorkflowManager implements WorkflowManagerInterface
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
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param PrioritizedServiceRegistryInterface $serviceRegistry
     * @param EventDispatcherInterface $eventDispatcher
     * @param string $class
     * @param TranslatorInterface $translator
     * @param string $noteIdentifier
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        PrioritizedServiceRegistryInterface $serviceRegistry,
        EventDispatcherInterface $eventDispatcher,
        $class,
        TranslatorInterface $translator,
        $noteIdentifier,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->serviceRegistry = $serviceRegistry;
        $this->eventDispatcher = $eventDispatcher;
        $this->class = $class;
        $this->translator = $translator;
        $this->noteIdentifier = $noteIdentifier;
        $this->tokenStorage = $tokenStorage;
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

    /**
     * {@inheritdoc}
     */
    public function changeState(ProposalInterface $proposal, $action, $params = [])
    {
        Assert::isInstanceOf($proposal, $this->class);

        $user = \Pimcore\Model\User::getById(0);
        $proxyUser = new User($user);
        $manager = \Pimcore\WorkflowManagement\Workflow\Manager\Factory::getManager($proposal, $user);

        /**
         * This is so stupid, Pimcores Workflow needs a valid token storage :/
         */
        $originalUser = $this->tokenStorage->getToken()->getUser();
        $this->tokenStorage->getToken()->setUser($proxyUser);

        $params['action'] = $action;

        if ($manager->validateAction($params['action'], $params['newState'], $params['newStatus'])) {
            try {
                $manager->performAction($params['action'], $params);
                \Pimcore\Logger::debug('CoreShop State update. ID: ' . $proposal->getId() . ', newState: "' . $params['newState'] . '", newStatus: "' . $params['newStatus'] .'"');
            } catch (\Exception $e) {
                throw new \Exception('changeOrderState Error: ' . $e->getMessage());
            }
        } else {
            throw new \Exception('changeOrderState Error: ' . $manager->getError());
        }

        $this->tokenStorage->getToken()->setUser($originalUser);

        return true;
    }
}