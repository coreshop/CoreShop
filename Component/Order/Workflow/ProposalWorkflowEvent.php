<?php

namespace CoreShop\Component\Order\Workflow;

use CoreShop\Component\Order\Model\ProposalInterface;
use Pimcore\Event\Traits\ArgumentsAwareTrait;
use Symfony\Component\EventDispatcher\Event;

class ProposalWorkflowEvent extends Event
{
    use ArgumentsAwareTrait;

    /**
     * @var ProposalInterface
     */
    private $proposal;

    /**
     * @var string
     */
    private $newState;

    /**
     * @var string
     */
    private $oldState;

    /**
     * @param ProposalInterface $proposal
     * @param string $newState
     * @param string $oldState
     */
    public function __construct(ProposalInterface $proposal, $newState, $oldState)
    {
        $this->proposal = $proposal;
        $this->newState = $newState;
        $this->oldState = $oldState;
    }

    /**
     * @return ProposalInterface
     */
    public function getProposal()
    {
        return $this->proposal;
    }

    /**
     * @param ProposalInterface $proposal
     */
    public function setProposal($proposal)
    {
        $this->proposal = $proposal;
    }

    /**
     * @return string
     */
    public function getNewState()
    {
        return $this->newState;
    }

    /**
     * @param string $newState
     */
    public function setNewState($newState)
    {
        $this->newState = $newState;
    }

    /**
     * @return string
     */
    public function getOldState()
    {
        return $this->oldState;
    }

    /**
     * @param string $oldState
     */
    public function setOldState($oldState)
    {
        $this->oldState = $oldState;
    }
}
