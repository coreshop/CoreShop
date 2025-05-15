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

namespace CoreShop\Bundle\WorkflowBundle\Applier;

use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManagerInterface;

final class StateMachineApplier implements StateMachineApplierInterface
{
    public function __construct(
        private StateMachineManagerInterface $stateMachineManager,
    ) {
    }

    public function apply($subject, ?string $workflowName = null, ?string $transition = null, bool $soft = true): void
    {
        $workflow = $this->stateMachineManager->get($subject, $workflowName);
        if ($soft === true) {
            if (!$workflow->can($subject, $transition)) {
                return;
            }
        }
        $workflow->apply($subject, $transition);
    }
}
