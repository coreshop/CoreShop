<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\OrderBundle\Expiration;

use CoreShop\Component\Order\Repository\OrderRepositoryInterface;

final class CartExpiration implements OrderExpirationInterface
{
    public function __construct(private OrderRepositoryInterface $cartRepository)
    {
    }

    public function expire(int $days, array $params = []): void
    {
        if ($days <= 0) {
            return;
        }

        $carts = $this->cartRepository->findExpiredCarts($days, $params['anonymous'], $params['customer']);

        foreach ($carts as $cart) {
            $cart->delete();
        }
    }
}
