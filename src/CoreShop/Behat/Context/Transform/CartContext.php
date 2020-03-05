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

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;

final class CartContext implements Context
{
    private $sharedStorage;
    private $cartRepository;
    private $cartContext;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        OrderRepositoryInterface $cartRepository,
        CartContextInterface $cartContext
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->cartRepository = $cartRepository;
        $this->cartContext = $cartContext;
    }

    /**
     * @Transform /^my cart/
     * @Transform /^cart(?:s)/
     */
    public function cart()
    {
        return $this->cartContext->getCart();
    }

    /**
     * @Transform /^my add-to-cart-form/
     * @Transform /^add-to-cart-form(?:|s)/
     */
    public function addToCartForm()
    {
        return $this->sharedStorage->get('add_to_cart_form');
    }

    /**
     * @Transform /^loaded cart(?:|s)/
     */
    public function loadedCart()
    {
        return $this->cartRepository->forceFind($this->cartContext->getCart()->getId());
    }
}
