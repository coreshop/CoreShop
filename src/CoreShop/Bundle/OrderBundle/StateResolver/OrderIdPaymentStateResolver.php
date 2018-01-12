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

namespace CoreShop\Bundle\OrderBundle\StateResolver;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;

class OrderIdPaymentStateResolver

{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var OrderPaymentStateResolver
     */
    private $orderPaymentResolver;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderPaymentStateResolver $orderPaymentResolver
     */
    public function __construct(OrderRepositoryInterface $orderRepository, OrderPaymentStateResolver $orderPaymentResolver)
    {
        $this->orderRepository = $orderRepository;
        $this->orderPaymentResolver = $orderPaymentResolver;
    }

    /**
     * @param $orderId
     */
    public function resolveOrder($orderId) {
        $order = $this->orderRepository->find($orderId);

        if (!$order instanceof OrderInterface) {
            return;
        }

        $this->orderPaymentResolver->resolve($order);
    }
}