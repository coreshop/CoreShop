<?php

namespace CoreShop\Component\Core\Model;

use CoreShop\Bundle\PayumBundle\Model\GatewayConfig;
use CoreShop\Component\Payment\Model\PaymentProvider as BasePaymentProvider;
use CoreShop\Component\Store\Model\StoreInterface as BaseStoreInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class PaymentProvider extends BasePaymentProvider implements PaymentProviderInterface
{
    /**
     * @var Collection
     */
    protected $stores;

    /**
     * @var GatewayConfig
     */
    protected $gatewayConfig;

    public function __construct()
    {
        parent::__construct();

        $this->stores = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getStores()
    {
        return $this->stores;
    }

    /**
     * {@inheritdoc}
     */
    public function hasStore(BaseStoreInterface $store)
    {
        return $this->stores->contains($store);
    }

    /**
     * {@inheritdoc}
     */
    public function addStore(BaseStoreInterface $store)
    {
        if (!$this->hasStore($store)) {
            $this->stores->add($store);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeStore(BaseStoreInterface $store)
    {
        if ($this->hasStore($store)) {
            $this->stores->removeElement($store);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setGatewayConfig(GatewayConfig $gatewayConfig)
    {
        $this->gatewayConfig = $gatewayConfig;
    }

    /**
     * @return GatewayConfig
     */
    public function getGatewayConfig()
    {
        return $this->gatewayConfig;
    }
}
