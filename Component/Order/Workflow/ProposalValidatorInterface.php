<?php

namespace CoreShop\Component\Order\Workflow;

use CoreShop\Component\Order\Model\ProposalInterface;

interface ProposalValidatorInterface 
{
    /**
     * @param ProposalInterface $proposal
     * @param $currentState
     * @param $newState
     * @return mixed
     */
    public function isValidForState(ProposalInterface $proposal, $currentState, $newState);
}