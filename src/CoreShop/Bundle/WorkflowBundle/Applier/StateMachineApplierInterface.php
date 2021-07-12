<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\WorkflowBundle\Applier;

interface StateMachineApplierInterface
{
    /**
     * @param mixed       $subject
     * @param string|null $workflowName
     * @param string|null $transition
     * @param bool        $soft
     */
    public function apply($subject, ?string $workflowName = null, ?string $transition = null, bool $soft = true): void;
}
