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

namespace CoreShop\Bundle\WorkflowBundle\Applier;

use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManagerInterface;

final class StateMachineApplier implements StateMachineApplierInterface
{
    public function __construct(private StateMachineManagerInterface $stateMachineManager)
    {
    }

    public function apply($subject, ?string $workflowName = null, ?string $transition = null, bool $soft = true): void
    {
        $workflow = $this->stateMachineManager->get($subject, $workflowName);
        if (true === $soft) {
            if (!$workflow->can($subject, $transition)) {
                return;
            }
        }
        $workflow->apply($subject, $transition);
    }
}
