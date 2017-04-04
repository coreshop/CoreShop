<?php

namespace CoreShop\Component\Order\Pimcore\Model;

use CoreShop\Component\Core\ImplementedByPimcoreException;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

class Cart extends AbstractPimcoreModel implements CartInterface, PimcoreModelInterface
{
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
    public function hasItems()
    {
        return is_array($this->getItems()) && count($this->getItems()) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function addItem($item)
    {
        $items = $this->getItems();
        $items[] = $item;

        $this->setItems($item);
    }

    /**
     * {@inheritdoc}
     */
    public function removeItem($item)
    {
        $items = $this->getItems();

        for ($i = 0; $i < count($items); $i++) {
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

        for ($i = 0; $i < count($items); $i++) {
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
    public function getCarrier()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setCarrier($carrier)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriceRules()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setPriceRules($priceRules)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function hasPriceRules()
    {
        return is_array($this->getPriceRules()) && count($this->getPriceRules()) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function addPriceRule($priceRule)
    {
        $items = $this->getPriceRules();
        $items[] = $priceRule;

        $this->setPriceRules($items);
    }

    /**
     * {@inheritdoc}
     */
    public function removePriceRule($priceRule)
    {
        $items = $this->getPriceRules();

        for ($i = 0; $i < count($items); $i++) {
            $arrayItem = $items[$i];

            if ($arrayItem->getId() === $priceRule->getId()) {
                unset($items[$i]);
                break;
            }
        }

        $this->setPriceRules($items);
    }

    /**
     * {@inheritdoc}
     */
    public function hasPriceRule($priceRule)
    {
        $items = $this->getPriceRules();

        for ($i = 0; $i < count($items); $i++) {
            $arrayItem = $items[$i];

            if ($arrayItem->getId() === $priceRule->getId()) {
                return true;
            }
        }

        return false;
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
}
