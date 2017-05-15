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

namespace CoreShop\Bundle\OrderBundle\Manager;

use CoreShop\Component\Resource\Pimcore\ObjectServiceInterface;
use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Customer\Context\CustomerNotFoundException;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Repository\CartRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Symfony\Component\HttpFoundation\Session\Attribute\NamespacedAttributeBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class CartManager implements CartManagerInterface
{
    const CART_ID_IDENTIFIER = 'cartId';
    const CART_OBJ_IDENTIFIER = 'cartObj';

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var FactoryInterface
     */
    protected $cartFactory;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var NamespacedAttributeBag
     */
    protected $sessionBag;

    /**
     * @var ObjectServiceInterface
     */
    protected $objectService;

    /**
     * @var string
     */
    protected $cartFolderPath;

    /**
     * @var CustomerContextInterface
     */
    protected $customerContext;

    /**
     * @param PimcoreRepositoryInterface $cartRepository
     * @param FactoryInterface $cartFactory
     * @param SessionInterface $session
     * @param ObjectServiceInterface $objectService
     * @param CustomerContextInterface $customerContext
     * @param string $cartFolderPath
     */
    public function __construct(
        PimcoreRepositoryInterface $cartRepository,
        FactoryInterface $cartFactory,
        SessionInterface $session,
        ObjectServiceInterface $objectService,
        CustomerContextInterface $customerContext,
        $cartFolderPath
    )
    {
        $this->cartRepository = $cartRepository;
        $this->session = $session;
        $this->cartFactory = $cartFactory;
        $this->sessionBag = $session->getBag('cart');
        $this->objectService = $objectService;
        $this->customerContext = $customerContext;
        $this->cartFolderPath = $cartFolderPath;
    }

    /**
     * {@inheritdoc}
     */
    public function getCart()
    {
        if ($this->hasCart()) {
            return $this->getSessionCart();
        }

        $cart = $this->createCart('default');

        $this->setCurrentCart($cart);

        return $cart;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCart()
    {
        return $this->getSessionCart() instanceof CartInterface;
    }

    /**
     * @return CartInterface|null
     */
    private function getSessionCart()
    {
        $cart = null;

        if ($this->sessionBag->has(self::CART_ID_IDENTIFIER) && $this->sessionBag->get(self::CART_ID_IDENTIFIER) !== 0) {
            $cart = $this->cartRepository->find($this->sessionBag->get(self::CART_ID_IDENTIFIER));
        }

        if (!$cart instanceof CartInterface && $this->sessionBag->has(self::CART_OBJ_IDENTIFIER)) {
            if ($this->sessionBag->get(self::CART_OBJ_IDENTIFIER) instanceof CartInterface) {
                $cart = $this->sessionBag->get(self::CART_OBJ_IDENTIFIER);
            }
        }

        return $cart;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentCart(CartInterface $cart)
    {
        if ($cart->getId() > 0) {
            $this->sessionBag->set(self::CART_ID_IDENTIFIER, $cart->getId());
        } else {
            $this->sessionBag->set(self::CART_OBJ_IDENTIFIER, $cart);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createCart($name, $customer = null, $store = null, $currency = null, $persist = false)
    {
        /**
         * @var CartInterface
         */
        $cart = $this->cartFactory->createNew();

        $cart->setKey(uniqid());
        $cart->setPublished(true);
        //$cart->setCustomer($customer); //TODO: Check Type
        //$cart->setStore($store);
        //$cart->setCurrency($currency);

        if ($persist) {
            $this->persistCart($cart);
        }

        return $cart;
    }

    /**
     * {@inheritdoc}
     */
    public function getStoredCarts($customer)
    {
        return $this->cartRepository->findForCustomer($customer);
    }

    /**
     * {@inheritdoc}
     */
    public function getByName($customer, $name)
    {
        return $this->cartRepository->findNamedForCustomer($customer, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteCart($cart)
    {
        if ($cart instanceof CartInterface) {
            $cart->delete();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function persistCart(CartInterface $cart)
    {
        if ($this->sessionBag->has(self::CART_OBJ_IDENTIFIER)) {
            $this->sessionBag->remove(self::CART_OBJ_IDENTIFIER);
        }

        $cartsFolder = $this->objectService->createFolderByPath(sprintf('%s/%s', $this->cartFolderPath, date('Y/m/d')));

        try {
            $cart->setCustomer($this->customerContext->getCustomer());
        } catch (CustomerNotFoundException $ex) {
            //Could happen that there is no customer yet, so we catch and continue
        }

        $cart->setParent($cartsFolder);
        $cart->save();

        $this->setCurrentCart($cart);
    }
}
