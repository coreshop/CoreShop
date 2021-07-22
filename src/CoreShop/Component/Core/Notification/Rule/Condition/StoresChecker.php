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

namespace CoreShop\Component\Core\Notification\Rule\Condition;

use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\PaymentInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Notification\Rule\Condition\AbstractConditionChecker;
use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\Store\Model\StoreAwareInterface;

class StoresChecker extends AbstractConditionChecker
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function isNotificationRuleValid($subject, $params, array $configuration)
    {
        $store = null;

        if ($subject instanceof StoreAwareInterface) {
            $store = $subject->getStore();
        } elseif ($subject instanceof OrderDocumentInterface) {
            $store = $subject->getOrder()->getStore();
        } elseif ($subject instanceof PaymentInterface) {
            $order = $subject->getOrder();

            if ($order instanceof OrderInterface) {
                $store = $order->getStore();
            }
        }

        if ($store instanceof StoreInterface) {
            return in_array($store->getId(), $configuration['stores']);
        }

        return false;
    }
}
