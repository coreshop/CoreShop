<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Notification\Rule\Condition;

use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\PaymentInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Notification\Rule\Condition\AbstractConditionChecker;
use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;
use CoreShop\Component\Store\Model\StoreAwareInterface;

class StoresChecker extends AbstractConditionChecker
{
    /**
     * @var StoreContextInterface
     */
    private $storeContext;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param StoreContextInterface $storeContext
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        StoreContextInterface $storeContext,
        OrderRepositoryInterface $orderRepository
    )
    {
        $this->storeContext = $storeContext;
        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function isNotificationRuleValid($subject, $params, array $configuration)
    {
        $store = null;

        if ($subject instanceof StoreAwareInterface) {
            $subject->getStore();
        } else if ($subject instanceof OrderDocumentInterface) {
            $store = $subject->getOrder()->getStore();
        } else if ($subject instanceof PaymentInterface) {
            $order = $subject->getOrder();

            if ($order instanceof OrderInterface) {
                $store = $order->getStore();
            }
        } else {
            try {
                return in_array($this->storeContext->getStore()->getId(), $configuration['stores']);
            } catch (StoreNotFoundException $ex) {
                return false;
            }
        }

        if ($store instanceof StoreInterface) {
            return in_array($store->getId(), $configuration['stores']);
        }

        return false;
    }
}
