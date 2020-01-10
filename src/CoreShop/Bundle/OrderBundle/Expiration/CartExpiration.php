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

namespace CoreShop\Bundle\OrderBundle\Expiration;

use CoreShop\Component\Order\Repository\CartRepositoryInterface;

final class CartExpiration implements ProposalExpirationInterface
{
    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(CartRepositoryInterface $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function expire($days, $params = [])
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
