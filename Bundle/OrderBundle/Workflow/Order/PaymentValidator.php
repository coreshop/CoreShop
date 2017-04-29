<?php

namespace CoreShop\Bundle\OrderBundle\Workflow\Order;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Workflow\ProposalValidatorInterface;
use CoreShop\Component\Order\Workflow\WorkflowManagerInterface;
use Webmozart\Assert\Assert;

class PaymentValidator implements ProposalValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function isValidForState(ProposalInterface $proposal, $currentState, $newState)
    {
        /**
         * @var $proposal OrderInterface
         */
        Assert::isInstanceOf($proposal, OrderInterface::class);

        if ($currentState === WorkflowManagerInterface::ORDER_STATUS_PAYMENT_REVIEW) {
            return false;
        }

        return true;
    }
}