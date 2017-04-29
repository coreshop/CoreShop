<?php

namespace CoreShop\Component\Order\Workflow\Order;

use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Workflow\ProposalValidatorInterface;

class ShipmentValidator implements ProposalValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function isValidForState(ProposalInterface $proposal, $currentState, $newState)
    {
        //TODO: implement me
        return true;
    }
}