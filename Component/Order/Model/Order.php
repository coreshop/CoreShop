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

use CoreShop\Component\Payment\Repository\PaymentRepositoryInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Resource\ImplementedByPimcoreException;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;
use Pimcore\Model\Object\Fieldcollection;

class Order extends AbstractPimcoreModel implements OrderInterface
{
    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function getPaymentFee($withTax = true)
    {
        return $withTax ? $this->getPaymentFeeGross() : $this->getPaymentFeeNet();
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function setPaymentFee($paymentFee, $withTax = true)
    {
        return $withTax ? $this->setPaymentFeeGross($paymentFee) : $this->setPaymentFeeNet($paymentFee);
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function getDiscount($withTax = true)
    {
        return $withTax ? $this->getDiscountGross() : $this->getDiscountNet();
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function setDiscount($discount, $withTax = true)
    {
        return $withTax ? $this->setDiscountGross($discount) : $this->setDiscountNet($discount);
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function getSubtotal($withTax = true)
    {
        return $withTax ? $this->getSubtotalGross() : $this->getSubtotalNet();
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function setSubtotal($subtotal, $withTax = true)
    {
        return $withTax ? $this->setSubtotalGross($subtotal) : $this->setSubtotalNet($subtotal);
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function getTotal($withTax = true)
    {
        return $withTax ? $this->getTotalGross() : $this->getTotalNet();
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function setTotal($total, $withTax = true)
    {
        return $withTax ? $this->setTotalGross($total) : $this->setTotalNet($total);
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function getShipping($withTax = true)
    {
        return $withTax ? $this->getShippingGross() : $this->getShippingNet();
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function setShipping($shipping, $withTax = true)
    {
        return $withTax ? $this->setShippingGross($shipping) : $this->setShippingNet($shipping);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalPayed()
    {
        $totalPayed = 0;

        foreach ($this->getPayments() as $payment) {
            if ($payment->getTotalAmount()) {
                $totalPayed += $payment->getTotalAmount();
            }
        }

        return $totalPayed;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsPayed()
    {
        return $this->getTotal() === $this->getTotalPayed();
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountPercentage()
    {
        $totalWithoutDiscount = $this->getSubtotal(false);
        $totalWithDiscount = $this->getSubtotal(false) - $this->getDiscount(false);

        return ((100 / $totalWithoutDiscount) * $totalWithDiscount) / 100;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalWeight()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalWeight($totalWeight)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderLanguage()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderLanguage($orderLanguage)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalNet()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalNet($total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalGross()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalGross($total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalTax()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalTax($totalTax)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubtotalNet()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setSubtotalNet($subTotalNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubtotalGross()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setSubtotalGross($subTotalGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubtotalTax()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setSubtotalTax($subtotalTax)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingNet()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingNet($total)
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
    public function setShippingGross($total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingTax()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingTax($shippingTax)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingTaxRate()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingTaxRate($taxRate)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountNet()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setDiscountNet($total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountGross()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setDiscountGross($total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxes($applyDiscountToTaxValues = true)
    {
        throw new \Exception('implement me');
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxes($taxes)
    {
        throw new \Exception('implement me');
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
    public function setPaymentFeeGross($paymentFeGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentFeeTaxRate()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentFeeTaxRate($taxRate)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderDate()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderDate($orderDate)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderNumber()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderNumber($orderNumber)
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
    public function getPriceRuleItems()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setPriceRuleItems($priceRuleItems)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function hasPriceRules()
    {
        return is_array($this->getPriceRuleItems()) && count($this->getPriceRuleItems()) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriceRules()
    {
        $rules = [];

        if ($this->getPriceRuleItems() instanceof Fieldcollection) {
            foreach ($this->getPriceRuleItems() as $ruleItem) {
                if ($ruleItem instanceof ProposalCartPriceRuleItem) {
                    $rules[] = $ruleItem->getCartPriceRule();
                }
            }
        }

        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function setPriceRules($priceRules)
    {
        if ($priceRules instanceof Fieldcollection) {
            $this->setPriceRuleItems($priceRules);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addPriceRule($priceRule)
    {
        if (!$this->hasPriceRule($priceRule)) {
            $items = $this->getPriceRuleItems();

            if (!$items instanceof Fieldcollection) {
                $items = new Fieldcollection();
            }

            $items->add($priceRule);

            $this->setPriceRules($items);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removePriceRule($priceRule)
    {
        $items = $this->getPriceRuleItems();

        if ($items instanceof Fieldcollection) {
            for ($i = 0; $i < count($items); ++$i) {
                $arrayItem = $items[$i];

                if ($arrayItem->getId() === $priceRule->getId()) {
                    $items->remove($i);
                    break;
                }
            }

            $this->setPriceRules($items);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasPriceRule($priceRule)
    {
        $items = $this->getPriceRuleItems();

        if ($items instanceof Fieldcollection) {
            foreach ($items as $item) {
                if ($item->getId() === $priceRule->getId()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return PaymentRepositoryInterface
     */
    private function getPaymentRepository()
    {
        return \Pimcore::getContainer()->get('coreshop.repository.payment');
    }

    /**
     * {@inheritdoc}
     */
    public function getPayments()
    {
        return $this->getPaymentRepository()->findForOrderId($this->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getItemForProduct(ProductInterface $product)
    {
        foreach ($this->getItems() as $item) {
            if ($item instanceof OrderItemInterface) {
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
