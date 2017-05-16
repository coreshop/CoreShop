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

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\ImplementedByPimcoreException;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;
use Webmozart\Assert\Assert;

class Cart extends AbstractPimcoreModel implements CartInterface
{
    use ProposalPriceRuleTrait;

    /**
     * {@inheritdoc}
     */
    public function getItemForProduct(PurchasableInterface $product)
    {
        foreach ($this->getItems() as $item) {
            if ($item instanceof CartItemInterface) {
                if ($item->getProduct() instanceof PurchasableInterface && $item->getProduct()->getId() === $product->getId()) {
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
     *  {@inheritdoc}
     */
    public function getPaymentFeeTaxRate()
    {
        //TODO: Use PaymentProvider TaxRule (still not implemented) to determine TaxRate
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentFee($withTax = true)
    {
        return $withTax ? $this->getPaymentFeeGross() : $this->getPaymentFeeNet();
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
     * calculates the total without discount.
     *
     * @param bool $withTax
     *
     * @return float
     */
    protected function getTotalWithoutDiscount($withTax = true)
    {
        $subtotal = $this->getSubtotal($withTax);
        $payment = $this->getPaymentFee($withTax);

        return $subtotal + $payment;
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
    public function getSubtotalTax()
    {
        return $this->getSubtotal(true) - $this->getSubtotal(false);
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
        Assert::isInstanceOf($item, CartItemInterface::class);

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
    public function getDiscount($withTax = true)
    {
        return $this->getContainer()->get('coreshop.cart.discount_calculator')->getDiscount($this, $withTax);
    }

    /**
     * {@inheritdoc}
     */
    public function getWeight()
    {
        $weight = 0;

        foreach ($this->getItems() as $item) {
            $weight += $item->getTotalWeight();
        }

        return $weight;
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected function getContainer()
    {
        return \Pimcore::getContainer();
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentProvider()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentProvider($paymentProvider)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
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

    /**
     * {@inheritdoc}
     */
    public function getPaymentFeeNet()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentFeeNet($paymentFeeNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentFeeGross()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentFeeGross($paymentFeeGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
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
    public function setOrder($order)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
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
}
