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

namespace CoreShop\Bundle\OrderBundle\Workflow;

use CoreShop\Bundle\WorkflowBundle\History\StateHistoryLoggerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use Symfony\Component\Workflow\Event\Event;

/**
 * @deprecated Class CoreShop\Bundle\OrderBundle\Workflow\OrderStateHistoryLogger is deprecated and will be removed with 2.1, use CoreShop\Bundle\WorkflowBundle\History\StateHistoryLoggerInterface instead
 */
final class OrderStateHistoryLogger
{
    /**
     * @var StateHistoryLoggerInterface
     */
    private $stateHistoryLogger;

    /**
     * @param StateHistoryLoggerInterface $stateHistoryLogger
     */
    public function __construct(StateHistoryLoggerInterface $stateHistoryLogger)
    {
        $this->stateHistoryLogger = $stateHistoryLogger;
    }

    /**
     * @param OrderInterface $order
     * @param Event          $event
     */
    public function log(OrderInterface $order, Event $event)
    {
        trigger_error(
            sprintf(
                '%s::%s is deprecated and will be removed with 2.1, please use %s:%s instead.',
                static::class,
                __METHOD__,
                StateHistoryLoggerInterface::class,
                __METHOD__
            ),
            E_USER_DEPRECATED
        );

        $this->stateHistoryLogger->log($order, $event);
    }
}
