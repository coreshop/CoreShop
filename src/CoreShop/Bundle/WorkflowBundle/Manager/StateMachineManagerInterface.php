<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\WorkflowBundle\Manager;

use Symfony\Component\Workflow\WorkflowInterface;

interface StateMachineManagerInterface
{
    public function get(object $subject, ?string $workflowName = null): WorkflowInterface;

    public function getTransitionFromState(WorkflowInterface $workflow, object $subject, string $fromState): ?string;

    public function getTransitionToState(WorkflowInterface $workflow, object $subject, string $toState): ?string;
}
