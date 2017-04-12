<?php

namespace CoreShop\Component\Core\Model;

use CoreShop\Bundle\PayumBundle\Model\PaymentProviderConfig;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use CoreShop\Component\Store\Model\StoreInterface as BaseStoreInterface;
use CoreShop\Component\Payment\Model\PaymentProvider as BasePaymentProvider;

class PaymentProvider extends BasePaymentProvider implements PaymentProviderInterface
{
    /**
     * @var Collection
     */
    protected $stores;

    /**
     * @var PaymentProviderConfig
     */
    protected $paymentProviderConfig;

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
    public function setPaymentProviderConfig(PaymentProviderConfig $paymentProviderConfig)
    {
        $this->paymentProviderConfig = $paymentProviderConfig;
    }

    /**
     * @return PaymentProviderConfig
     */
    public function getPaymentProviderConfig()
    {
        return $this->paymentProviderConfig;
    }
}
