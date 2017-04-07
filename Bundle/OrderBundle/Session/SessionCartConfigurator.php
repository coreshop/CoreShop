<?php

namespace CoreShop\Bundle\OrderBundle\Session;

use Pimcore\Session\SessionConfiguratorInterface;
use Symfony\Component\HttpFoundation\Session\Attribute\NamespacedAttributeBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionCartConfigurator implements SessionConfiguratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function configure(SessionInterface $session)
    {
        $bag = new NamespacedAttributeBag('coreshop_session_cart');
        $bag->setName('cart');

        $session->registerBag($bag);
    }
}
