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

namespace CoreShop\Bundle\OrderBundle\Workflow\Cart;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Processable\ProcessableInterface;
use CoreShop\Component\Order\Repository\CartRepositoryInterface;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\Pimcore\VersionHelper;
use Webmozart\Assert\Assert;

final class CartRecoverer
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var ProcessableInterface
     */
    private $cartRepository;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param CartRepositoryInterface  $cartRepository
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        CartRepositoryInterface $cartRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->cartRepository = $cartRepository;
    }

    /**
     * @param  $orderId
     */
    public function recover($orderId)
    {
        $proposal = $this->orderRepository->find($orderId);

        /**
         * @var $proposal OrderInterface
         */
        Assert::isInstanceOf($proposal, OrderInterface::class);

        $cart = $this->cartRepository->findCartByOrder($proposal);
        if ($cart instanceof CartInterface) {
            $cart->setOrder(null);
            VersionHelper::useVersioning(function () use ($cart) {
                $cart->save();
            }, false);
        }
    }
}
