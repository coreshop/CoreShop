<?php

namespace CoreShop\Bundle\CoreBundle\Factory;

use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;

class CartFactory implements FactoryInterface
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var StoreContextInterface
     */
    private $storeContext;

    /**
     * @var CurrencyContextInterface
     */
    private $currencyContext;

    /**
     * @param string $className
     * @param CurrencyContextInterface $currencyContext
     * @param StoreContextInterface $storeContext
     */
    public function __construct(
        $className,
        CurrencyContextInterface $currencyContext,
        StoreContextInterface $storeContext
    )
    {
        $this->className = $className;
        $this->currencyContext = $currencyContext;
        $this->storeContext = $storeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        $cart = new $this->className();

        if ($cart instanceof CartInterface) {
            $cart->setStore($this->storeContext->getStore());
            $cart->setCurrency($this->currencyContext->getCurrency());
        }

        return $cart;
    }
}