<?php

namespace CoreShop\Bundle\OrderBundle\Manager;

use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Component\Core\Pimcore\ObjectServiceInterface;
use Symfony\Component\HttpFoundation\Session\Attribute\NamespacedAttributeBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartManager implements CartManagerInterface
{
    const CART_ID_IDENTIFIER = 'cartId';
    const CART_OBJ_IDENTIFIER = 'cartObj';

    /**
     * @var PimcoreRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var FactoryInterface
     */
    private $cartFactory;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var NamespacedAttributeBag
     */
    private $sessionBag;

    /**
     * @var ObjectServiceInterface
     */
    private $objectService;

    /**
     * @var string
     */
    private $cartFolderPath;

    /**
     * @param PimcoreRepositoryInterface $cartRepository
     * @param FactoryInterface           $cartFactory
     * @param SessionInterface           $session
     * @param ObjectServiceInterface     $objectService
     * @param string                     $cartFolderPath
     */
    public function __construct(
        PimcoreRepositoryInterface $cartRepository,
        FactoryInterface $cartFactory,
        SessionInterface $session,
        ObjectServiceInterface $objectService,
        $cartFolderPath)
    {
        $this->cartRepository = $cartRepository;
        $this->session = $session;
        $this->cartFactory = $cartFactory;
        $this->sessionBag = $session->getBag('cart');
        $this->objectService = $objectService;
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

        if ($this->session->has(self::CART_ID_IDENTIFIER) && $this->session->get(self::CART_ID_IDENTIFIER) !== 0) {
            $cart = $this->cartRepository->find($this->session->get(self::CART_ID_IDENTIFIER));
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
     *
     * todo: refactor, should be done by the repository
     */
    public function getStoredCarts($customer)
    {
        $list = $this->cartRepository->getListingClass();
        $list->setCondition('customer__id = ? AND order__id is null', [$customer->getId()]);
        $list->load();

        return $list->getObjects();
    }

    /**
     * {@inheritdoc}
     *
     * todo: refactor, should be done by the repository
     */
    public function getByName($customer, $name)
    {
        $list = $this->cartRepository->getListingClass();
        $list->setCondition('user__id = ? AND name = ? AND order__id is null', [$customer->getId(), $name]);
        $list->load();

        if ($list->getTotalCount() > 0) {
            $objects = $list->getObjects();

            return $objects[0];
        }

        return null;
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
        $cartsFolder = $this->objectService->createFolderByPath(sprintf('%s/%s', $this->cartFolderPath, date('Y/m/d')));
        $cart->setParent($cartsFolder);
        $cart->save();
    }
}
