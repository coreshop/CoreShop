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
     * @param string            $newState
     * @param string            $oldState
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
