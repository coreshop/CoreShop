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

namespace CoreShop\Bundle\OrderBundle\Expiration;

use CoreShop\Component\Order\Repository\OrderRepositoryInterface;

final class CartExpiration implements OrderExpirationInterface
{
    private OrderRepositoryInterface $cartRepository;

    public function __construct(OrderRepositoryInterface $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    public function expire(int $days, array $params = []): void
    {
        if ($days <= 0) {
            return;
        }

        $carts = $this->cartRepository->findExpiredCarts($days, $params['anonymous'], $params['customer']);

        if (is_array($carts)) {
            foreach ($carts as $cart) {
                $cart->delete();
            }
        }
    }
}
