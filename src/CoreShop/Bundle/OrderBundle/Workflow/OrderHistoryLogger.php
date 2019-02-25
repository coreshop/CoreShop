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

namespace CoreShop\Bundle\OrderBundle\Workflow;

use CoreShop\Bundle\WorkflowBundle\History\HistoryLoggerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;

/**
 * @deprecated Class CoreShop\Bundle\OrderBundle\Workflow\OrderHistoryLogger is deprecated and will be removed with 2.1, use CoreShop\Bundle\WorkflowBundle\History\HistoryLoggerInterface instead
 */
final class OrderHistoryLogger
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var HistoryLoggerInterface
     */
    private $historyLogger;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param HistoryLoggerInterface   $historyLogger
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        HistoryLoggerInterface $historyLogger
    ) {
        $this->orderRepository = $orderRepository;
        $this->historyLogger = $historyLogger;
    }

    /**
     * @param null $orderId
     * @param null $message
     * @param null $description
     * @param bool $translate
     */
    public function log($orderId = null, $message = null, $description = null, $translate = false)
    {
        trigger_error(
            sprintf('%s::%s is deprecated and will be removed with 2.1, please use %s:%s instead.',
                static::class,
                __METHOD__,
                HistoryLoggerInterface::class,
                __METHOD__
            ),
            E_USER_DEPRECATED
        );

        $order = $this->orderRepository->find($orderId);
        if (!$order instanceof OrderInterface) {
            return;
        }

        $this->historyLogger->log($order, $message, $description, $translate);
    }
}
