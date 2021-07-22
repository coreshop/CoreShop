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

namespace CoreShop\Bundle\OrderBundle\Context;

use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Order\Context\CartNotFoundException;
use CoreShop\Component\Order\Repository\CartRepositoryInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class SessionAndStoreBasedCartContext implements CartContextInterface
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var string
     */
    private $sessionKeyName;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var StoreContextInterface
     */
    private $storeContext;

    /**
     * @param SessionInterface        $session
     * @param string                  $sessionKeyName
     * @param CartRepositoryInterface $cartRepository
     * @param StoreContextInterface   $storeContext
     */
    public function __construct(
        SessionInterface $session,
        string $sessionKeyName,
        CartRepositoryInterface $cartRepository,
        StoreContextInterface $storeContext
    ) {
        $this->session = $session;
        $this->sessionKeyName = $sessionKeyName;
        $this->cartRepository = $cartRepository;
        $this->storeContext = $storeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getCart()
    {
        try {
            $store = $this->storeContext->getStore();
        } catch (StoreNotFoundException $exception) {
            throw new CartNotFoundException($exception);
        }

        if (!$this->session->has(sprintf('%s.%s', $this->sessionKeyName, $store->getId()))) {
            throw new CartNotFoundException('CoreShop was not able to find the cart in session');
        }

        $cart = $this->cartRepository->findCartById($this->session->get(sprintf('%s.%s', $this->sessionKeyName, $store->getId())));

        if (null === $cart || null === $cart->getStore() || $cart->getStore()->getId() !== $store->getId()) {
            $cart = null;
        }

        if (null === $cart) {
            $this->session->remove(sprintf('%s.%s', $this->sessionKeyName, $store->getId()));

            throw new CartNotFoundException('CoreShop was not able to find the cart in session');
        }

        return $cart;
    }
}
