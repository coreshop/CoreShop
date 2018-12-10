<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\EventListener\NotificationRules;

use CoreShop\Component\Order\Model\OrderDocumentInterface;
use Symfony\Component\Workflow\Event\Event;
use Webmozart\Assert\Assert;

final class OrderDocumentWorkflowListener extends AbstractNotificationRuleListener
{
    /**
     * @var string
     */
    private $type;

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @param Event $event
     */
    public function applyDocumentWorkflowTransitionCompleted(Event $event)
    {
        $subject = $event->getSubject();

        /**
         * @var $subject OrderDocumentInterface
         */
        Assert::implementsInterface($subject, OrderDocumentInterface::class);

        $this->rulesProcessor->applyRules($this->type, $subject, [
            'order' => $subject->getOrder(),
            'fromState' => $event->getMarking()->getPlaces(),
            'toState' => $event->getTransition()->getTos(),
            'transition' => $event->getTransition()->getName(),
        ]);
    }
}
