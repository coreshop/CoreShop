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

use CoreShop\Component\Currency\Model\CurrencyAwareTrait;
use CoreShop\Component\Resource\ImplementedByPimcoreException;
use CoreShop\Component\StorageList\Model\StorageListProductInterface;
use CoreShop\Component\Store\Model\StoreAwareTrait;
use CoreShop\Component\Taxation\Model\TaxItemInterface;
use Pimcore\Model\DataObject\Fieldcollection;
use Webmozart\Assert\Assert;

class Cart extends AbstractProposal implements CartInterface
{
    use ProposalPriceRuleTrait;
    use StoreAwareTrait;
    use CurrencyAwareTrait;
    use AdjustableTrait;

    /**
     * {@inheritdoc}
     */
    public function getItemForProduct(StorageListProductInterface $product)
    {
        Assert::isInstanceOf($product, PurchasableInterface::class);

        foreach ($this->getItems() as $item) {
            if ($item instanceof CartItemInterface) {
                if ($item->getProduct() instanceof PurchasableInterface && $item->getProduct()->getId(
                    ) === $product->getId()) {
                    return $item;
                }
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalTax()
    {
        if (!$this->getTaxes() instanceof Fieldcollection) {
            return 0;
        }

        $totalTax = 0;

        foreach ($this->getTaxes()->getItems() as $taxItem) {
            if (!$taxItem instanceof TaxItemInterface) {
                continue;
            }

            $totalTax += $taxItem->getAmount();
        }

        return $totalTax;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal($withTax = true)
    {
        $total = $this->getSubtotal($withTax);

        return $total + $this->getAdjustmentsTotal(null, $withTax);
    }

    /**
     * {@inheritdocs}
     */
    public function getDiscountPercentage()
    {
        $totalDiscount = $this->getDiscount();
        $totalWithoutDiscount = $this->getSubtotal();

        if ($totalWithoutDiscount > 0) {
            return $totalDiscount / $totalWithoutDiscount;
        }

        return 0;
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
        $subtotalTax = 0;

        foreach ($this->getItems() as $item) {
            if ($item instanceof CartItemInterface) {
                $subtotalTax += $item->getTotalTax();
            }
        }

        return $subtotalTax;
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
     * {@inheritdoc}
     */
    public function getDiscount($withTax = true)
    {
        return $withTax ? $this->getAdjustmentsTotal(AdjustmentInterface::CART_PRICE_RULE, true) : $this->getAdjustmentsTotal(AdjustmentInterface::CART_PRICE_RULE, false);
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
    protected function recalculateAfterAdjustmentChange()
    {

    }
}
