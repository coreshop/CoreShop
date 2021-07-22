<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\WorkflowBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class WorkflowTransitionEvent extends Event
{
    protected array $allowedTransitions;
    protected string $workflowName;

    public function __construct(array $allowedTransitions, string $workflowName)
    {
        $this->allowedTransitions = $allowedTransitions;
        $this->workflowName = $workflowName;
    }

    public function getWorkflowName(): string
    {
        return $this->workflowName;
    }

    public function addAllowedTransitions(array $allowedTransitions): void
    {
        $this->allowedTransitions = array_merge($this->allowedTransitions, $allowedTransitions);
    }

    public function setAllowedTransitions(array $allowedTransitions): void
    {
        $this->allowedTransitions = $allowedTransitions;
    }

    public function getAllowedTransitions(): array
    {
        return $this->allowedTransitions;
    }
}

