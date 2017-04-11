<?php

namespace CoreShop\Component\Order\Pimcore\Model;

use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Resource\ImplementedByPimcoreException;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use CoreShop\Component\Taxation\Model\TaxRateInterface;

class Cart extends AbstractPimcoreModel implements CartInterface
{
     /**
     * {@inheritdoc}
     */
    public function getItemForProduct(ProductInterface $product) {
        foreach ($this->getItems() as $item) {
            if ($item instanceof CartItemInterface) {
                if ($item->getProduct() instanceof ProductInterface && $item->getProduct()->getId() === $product->getId()) {
                    return $item;
                }
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalTax($withTax = true)
    {
        return $this->getTotal(true) - $this->getTotal(false);
    }

    /**
     * {@inheritdoc}
     */
    public function getShipping($withTax = true)
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount($withTax = true)
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxes($applyDiscountToTaxValues = true)
    {
        $usedTaxes = [];
        $taxCollector = \Pimcore::getContainer()->get('coreshop.collector.taxes');

        foreach ($this->getItems() as $item) {
            if ($item instanceof CartItemInterface) {
                $usedTaxes = $taxCollector->mergeTaxes($item->getTaxes(), $usedTaxes);
            }
        }

        /* TODO: collect taxes of this stuff as well if (!$this->getFreeShipping()) {
            $shippingProvider = $this->getShippingProvider();

            if ($shippingProvider instanceof Carrier) {
                $shippingTax = $this->getShippingProvider()->getTaxCalculator();

                if ($shippingTax instanceof TaxCalculator) {
                    $taxesAmount = $shippingTax->getTaxesAmount($this->getShipping(false), true);

                    if (is_array($taxesAmount)) {
                        foreach ($taxesAmount as $id => $amount) {
                            $addTax(Tax::getById($id), $amount);
                        }
                    }
                }
            }
        }

        $paymentProvider = $this->getPaymentProvider();

        if ($paymentProvider instanceof PaymentPlugin) {
            if ($paymentProvider->getPaymentTaxCalculator($this) instanceof TaxCalculator) {
                $taxesAmount = $paymentProvider->getPaymentTaxCalculator($this)->getTaxesAmount($this->getPaymentFee(false), true);

                if(is_array($taxesAmount)) {
                    foreach ($taxesAmount as $id => $amount) {
                        $addTax(Tax::getById($id), $amount);
                    }
                }
            }
        }*/

        return $usedTaxes;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentFee($withTax = true)
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal($withTax = true)
    {
        $total = $this->getTotalWithoutDiscount($withTax);
        $discount = $this->getDiscount($withTax);

        return $total - $discount;
    }

    /**
     * calculates the total without discount
     *
     * @param bool $withTax
     * @return float
     */
    private function getTotalWithoutDiscount($withTax = true)
    {
        $subtotal = $this->getSubtotal($withTax);
        $shipping = $this->getShipping($withTax);
        $payment = $this->getPaymentFee($withTax);

        return $subtotal + $shipping + $payment;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubtotal($withTax = true)
    {
        $subtotal = 0;

        foreach ($this->getItems() as $item) {
            if ($item instanceof CartItemInterface) {
                $subtotal += $item->getTotal($withTax);
            }
        }

        return $subtotal;
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

        $this->setItems($items);
    }

    /**
     * {@inheritdoc}
     */
    public function removeItem($item)
    {
        $items = $this->getItems();

        for ($i = 0; $i < count($items); ++$i) {
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

        for ($i = 0; $i < count($items); ++$i) {
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

        for ($i = 0; $i < count($items); ++$i) {
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

        for ($i = 0; $i < count($items); ++$i) {
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

    /**
     * {@inheritdoc}
     */
    public function getCurrentStep()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentStep($name)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }
}
