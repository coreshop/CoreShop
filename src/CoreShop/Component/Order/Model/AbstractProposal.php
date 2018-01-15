<?php

namespace CoreShop\Component\Order\Model;

use CoreShop\Bundle\PaymentBundle\Model\PaymentDataInterface;
use CoreShop\Bundle\PaymentBundle\Model\PaymentSettings;
use CoreShop\Component\Currency\Model\CurrencyAwareTrait;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Resource\ImplementedByPimcoreException;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;
use CoreShop\Component\Store\Model\StoreAwareTrait;
use Pimcore\Model\DataObject\Objectbrick\Data\CoreShopPaymentData;
use Webmozart\Assert\Assert;

abstract class AbstractProposal extends AbstractPimcoreModel implements ProposalInterface
{
    use StoreAwareTrait;
    use CurrencyAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function hasItems()
    {
        return is_array($this->getItems()) && count($this->getItems()) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function addItem($item)
    {
        Assert::isInstanceOf($item, ProposalItemInterface::class);

        $items = $this->getItems();
        $items[] = $item;

        $this->setItems($items);
    }

    /**
     * {@inheritdoc}
     */
    public function removeItem($item)
    {
        $items = $this->getItems();

        for ($i = 0, $c = count($items); $i < $c; ++$i) {
            $arrayItem = $items[$i];

            if ($arrayItem->getId() === $item->getId()) {
                unset($items[$i]);
                break;
            }
        }

        $this->setItems($items);
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem($item)
    {
        $items = $this->getItems();

        for ($i = 0, $c = count($items); $i < $c; ++$i) {
            $arrayItem = $items[$i];

            if ($arrayItem->getId() === $item->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingGross()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getStore()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setStore($store)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentData()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentData($paymentData)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentProvider()
    {
        $paymentData = $this->getPaymentData()->getCoreShopPaymentData();
        if ($paymentData instanceof PaymentDataInterface) {
            return $paymentData->getPaymentProvider();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentProvider(PaymentProviderInterface $paymentProvider)
    {
        $paymentData = $this->getPaymentData()->getCoreShopPaymentData();
        if (!$paymentData instanceof PaymentDataInterface) {
            $paymentData = new CoreShopPaymentData($this);
        }

        $paymentData->setPaymentProvider($paymentProvider);
        $this->getPaymentData()->setCoreShopPaymentData($paymentData);

        return $paymentData;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentProviderSettings()
    {
        $paymentData = $this->getPaymentData()->getCoreShopPaymentData();
        $settings = [];
        if ($paymentData instanceof PaymentDataInterface) {
            $settings = $paymentData->getSettings();
        }

        $data = new PaymentSettings($settings);
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentProviderSettings(PaymentSettings $paymentSettings)
    {
        $paymentData = $this->getPaymentData()->getCoreShopPaymentData();
        if (!$paymentData instanceof PaymentDataInterface) {
            $paymentData = new CoreShopPaymentData($this);
        }

        $paymentData->setSettings($paymentSettings->getValuesForDb());
        $this->getPaymentData()->setCoreShopPaymentData($paymentData);

        return $paymentData;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomer()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomer($customer)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingAddress()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingAddress($shippingAddress)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getInvoiceAddress()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setInvoiceAddress($invoiceAddress)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency($currency)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setItems($items)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxes()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxes($taxes)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getComment()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setComment($comment)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdditionalData()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdditionalData($additionalData)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }
}