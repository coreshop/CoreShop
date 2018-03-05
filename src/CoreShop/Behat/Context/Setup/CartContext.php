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

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\StorageList\StorageListModifierInterface;

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
     * @var StorageListModifierInterface
     */
    private $cartModifier;

    /**
     * @var CartManagerInterface
     */
    private $cartManager;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param CartContextInterface $cartContext
     * @param StorageListModifierInterface $cartModifier
     * @param CartManagerInterface $cartManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        CartContextInterface $cartContext,
        StorageListModifierInterface $cartModifier,
        CartManagerInterface $cartManager
    )
    {
        $this->sharedStorage = $sharedStorage;
        $this->cartContext = $cartContext;
        $this->cartModifier = $cartModifier;
        $this->cartManager = $cartManager;
    }

    /**
     * @Given /^I add the (product "[^"]+") to my cart$/
     */
    public function addProductToCart(ProductInterface $product)
    {
        $cart = $this->cartContext->getCart();

        $this->cartModifier->addItem($cart, $product);

        $this->cartManager->persistCart($cart);
    }
}
