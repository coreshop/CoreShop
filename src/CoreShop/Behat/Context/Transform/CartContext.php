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

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Order\Context\CartContextInterface;

final class CartContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var CartContextInterface
     */
    private $cartContext;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param CartContextInterface   $cartContext
     */
    public function __construct(SharedStorageInterface $sharedStorage, CartContextInterface $cartContext)
    {
        $this->sharedStorage = $sharedStorage;
        $this->cartContext = $cartContext;
    }

    /**
     * @Transform /^my cart/
     */
    public function cart()
    {
        return $this->cartContext->getCart();
    }
}
