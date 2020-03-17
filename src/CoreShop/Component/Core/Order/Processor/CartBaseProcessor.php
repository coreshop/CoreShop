<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Core\Order\Processor;

use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;
use Pimcore\Model\DataObject\Fieldcollection;

final class CartBaseProcessor implements CartProcessorInterface
{
    protected $currencyConverter;

    public function __construct(CurrencyConverterInterface $currencyConverter)
    {
        $this->currencyConverter = $currencyConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function process(OrderInterface $cart): void
    {
        $cart->removeBaseAdjustmentsRecursively();

        $cart->setBaseCurrency($cart->getStore()->getCurrency());

        foreach ([true, false] as $withTax) {
            $subtotal = $cart->getSubtotal($withTax);
            $total = $cart->getTotal($withTax);

            $cart->setBaseTotal($subtotal, $withTax);
            $cart->setTotal($total, $withTax);

            $cart->setSubtotal($this->convert($subtotal, $cart), $withTax);
            $cart->setTotal($this->convert($total, $cart), $withTax);
        }

        foreach ($cart->getItems() as $item) {
            foreach ([true, false] as $withTax) {
                $itemRetailPrice = $item->getItemRetailPrice($withTax);
                $itemPrice = $item->getTotal($withTax);
                $total = $item->getItemPrice($withTax);

                $item->setBaseItemRetailPrice($itemRetailPrice, $withTax);
                $item->setBaseTotal($itemPrice, $withTax);
                $item->setBaseItemPrice($total, $withTax);

                $item->setItemRetailPrice($this->convert($itemRetailPrice, $cart), $withTax);
                $item->setTotal($this->convert($itemPrice, $cart), $withTax);
                $item->setItemPrice($this->convert($total, $cart), $withTax);
            }

            $itemTax = $item->getItemTax();

            $item->setBaseItemTax($itemTax);
            $item->setItemTax($this->convert($itemTax, $cart));

            foreach ($item->getAdjustments() as $adjustment) {
                $adjustmentNet = $adjustment->getAmount(false);
                $adjustmentGross = $adjustment->getAmount(true);

                $baseAdjustment = clone $adjustment;

                $baseAdjustment->setAmount($adjustmentGross, $adjustmentNet);
                $adjustment->setAmount(
                    $this->convert($baseAdjustment->getAmount(true), $cart),
                    $this->convert($baseAdjustment->getAmount(false), $cart)
                );

                $item->addBaseAdjustment($baseAdjustment);
            }

            $baseItemTaxesFieldCollection = new Fieldcollection();

            if ($item->getTaxes() instanceof Fieldcollection) {
                foreach ($item->getTaxes()->getItems() as $taxItem) {
                    if ($taxItem instanceof TaxItemInterface) {
                        $taxAmount = $taxItem->getAmount();

                        $baseItem = clone $taxItem;
                        $baseItem->setAmount($taxAmount);

                        $taxItem->setAmount($this->convert($taxAmount, $cart));

                        $baseItemTaxesFieldCollection->add($baseItem);
                    }
                }
            }

            $item->setBaseTaxes($baseItemTaxesFieldCollection);
        }

        foreach ($cart->getAdjustments() as $adjustment) {
            $adjustmentNet = $adjustment->getAmount(false);
            $adjustmentGross = $adjustment->getAmount(true);

            $baseAdjustment = clone $adjustment;

            $baseAdjustment->setAmount($adjustmentGross, $adjustmentNet);
            $adjustment->setAmount(
                $this->convert($baseAdjustment->getAmount(true), $cart),
                $this->convert($baseAdjustment->getAmount(false), $cart)
            );

            $cart->addBaseAdjustment($baseAdjustment);
        }

        $baseTaxesFieldCollection = new Fieldcollection();

        if ($cart->getTaxes() instanceof Fieldcollection) {
            foreach ($cart->getTaxes()->getItems() as $item) {
                if ($item instanceof TaxItemInterface) {
                    $taxAmount = $taxItem->getAmount();

                    $baseItem = clone $taxItem;
                    $baseItem->setAmount($taxAmount);

                    $taxItem->setAmount($this->convert($taxAmount, $cart));

                    $baseTaxesFieldCollection->add($baseItem);
                }
            }
        }

        $cart->setBaseTaxes($baseTaxesFieldCollection);
    }

    protected function convert(?int $value, OrderInterface $cart)
    {
        if (null === $value) {
            return 0;
        }

        if (!$cart->getBaseCurrency() instanceof CurrencyInterface) {
            return $value;
        }

        return $this->currencyConverter->convert(
            $value,
            $cart->getBaseCurrency()->getIsoCode(),
            $cart->getCurrency()->getIsoCode()
        );
    }
}
