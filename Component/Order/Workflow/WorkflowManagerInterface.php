<?php

namespace CoreShop\Component\Order\Workflow;

use CoreShop\Component\Order\Model\ProposalInterface;

interface WorkflowManagerInterface
{
    const ORDER_STATE_INITIALIZED = 'initialized';
    const ORDER_STATE_NEW = 'new';
    const ORDER_STATE_PENDING_PAYMENT = 'pending_payment';
    const ORDER_STATE_PROCESSING = 'processing';
    const ORDER_STATE_COMPLETE = 'complete';
    const ORDER_STATE_CLOSED = 'closed';
    const ORDER_STATE_CANCELED = 'canceled';
    const ORDER_STATE_ON_HOLD = 'holded';
    const ORDER_STATE_PAYMENT_REVIEW = 'payment_review';

    const ORDER_STATUS_INITIALIZED = 'initialized';
    const ORDER_STATUS_PENDING = 'pending';
    const ORDER_STATUS_PENDING_PAYMENT = 'pending_payment';
    const ORDER_STATUS_PROCESSING = 'processing';
    const ORDER_STATUS_COMPLETE = 'complete';
    const ORDER_STATUS_CLOSED = 'closed';
    const ORDER_STATUS_CANCELED = 'canceled';
    const ORDER_STATUS_ON_HOLD = 'on_hold';
    const ORDER_STATUS_PAYMENT_REVIEW = 'payment_review';

    /**
     * @param ProposalValidatorInterface $proposalValidator
     * @param $priority
     */
    public function addValidator(ProposalValidatorInterface $proposalValidator, $priority);

    /**
     * @param ProposalInterface $proposal
     * @param $currentState
     * @param $newState
     * @return mixed
     */
    public function validateNewState(ProposalInterface $proposal, $currentState, $newState);

    /**
     * @param ProposalInterface $proposal
     * @param $newState
     * @param $currentState
     * @return mixed
     */
    public function beforeWorkflowDispatch(ProposalInterface $proposal, $newState, $currentState);

    /**
     * @param ProposalInterface $proposal
     * @param $newState
     * @param $oldState
     * @return mixed
     */
    public function successWorkflowDispatch(ProposalInterface $proposal, $newState, $oldState);

    /**
     * @param ProposalInterface $proposal
     * @param $newState
     * @param $oldState
     * @return mixed
     */
    public function failureWorkflowDispatch(ProposalInterface $proposal, $newState, $oldState);

    /**
     * {@inheritdoc}
     */
    public function getStateHistory(ProposalInterface $proposal);

    /**
     * {@inheritdoc}
     */
    public function getCurrentState(ProposalInterface $proposal);
}