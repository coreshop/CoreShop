<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\OrderBundle\Event;

use CoreShop\Bundle\WorkflowBundle\Event\WorkflowTransitionEvent as NewWorkflowTransitionEvent;

if (class_exists(NewWorkflowTransitionEvent::class)) {
    @trigger_error('Class CoreShop\Bundle\OrderBundle\Event\WorkflowTransitionEvent is deprecated since version 2.2.6 and will be removed in 3.0.0. Use CoreShop\Bundle\WorkflowBundle\Event\WorkflowTransitionEvent class instead.', E_USER_DEPRECATED);
} else {
    /**
     * @deprecated Class CoreShop\Bundle\OrderBundle\Event\WorkflowTransitionEvent is deprecated since version 2.2.6 and will be removed in 3.0.0. Use CoreShop\Bundle\WorkflowBundle\Event\WorkflowTransitionEvent class instead.
     */
    class WorkflowTransitionEvent
    {
    }
}
