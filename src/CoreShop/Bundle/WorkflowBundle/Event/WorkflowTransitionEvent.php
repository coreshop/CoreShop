<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\WorkflowBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class WorkflowTransitionEvent extends Event
{
    protected array $allowedTransitions;

    public function __construct(
        array $allowedTransitions,
        protected string $workflowName,
    ) {
        $this->allowedTransitions = $allowedTransitions;
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
