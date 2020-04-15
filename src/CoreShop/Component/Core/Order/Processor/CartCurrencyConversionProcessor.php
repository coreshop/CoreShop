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

final class CartCurrencyConversionProcessor implements CartProcessorInterface
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
        $cart->removeConvertedAdjustmentsRecursively();

        $cart->setBaseCurrency($cart->getStore()->getCurrency());

        foreach ([true, false] as $withTax) {
            $subtotal = $cart->getSubtotal($withTax);
            $total = $cart->getTotal($withTax);

            $cart->setConvertedSubtotal($this->convert($subtotal, $cart), $withTax);
            $cart->setConvertedTotal($this->convert($total, $cart), $withTax);
        }

        foreach ($cart->getItems() as $item) {
            foreach ([true, false] as $withTax) {
                $itemRetailPrice = $item->getItemRetailPrice($withTax);
                $itemDiscountPrice = $item->getItemDiscountPrice($withTax);
                $itemDiscount = $item->getItemDiscount($withTax);
                $itemPrice = $item->getTotal($withTax);
                $total = $item->getItemPrice($withTax);

                $item->setConvertedItemRetailPrice($this->convert($itemRetailPrice, $cart), $withTax);
                $item->setConvertedTotal($this->convert($itemPrice, $cart), $withTax);
                $item->setConvertedItemPrice($this->convert($total, $cart), $withTax);
                $item->setConvertedItemDiscount($this->convert($itemDiscount, $cart), $withTax);
                $item->setConvertedItemDiscountPrice($this->convert($itemDiscountPrice, $cart), $withTax);
            }

            $itemTax = $item->getItemTax();

            $item->setConvertedItemTax($this->convert($itemTax, $cart));

            foreach ($item->getAdjustments() as $adjustment) {
                $convertedAdjustment = clone $adjustment;

                $convertedAdjustment->setAmount(
                    $this->convert($convertedAdjustment->getAmount(true), $cart),
                    $this->convert($convertedAdjustment->getAmount(false), $cart)
                );

                $item->addConvertedAdjustment($convertedAdjustment);
            }

            $convertedItemTaxesFieldCollection = new Fieldcollection();

            if ($item->getTaxes() instanceof Fieldcollection) {
                foreach ($item->getTaxes()->getItems() as $taxItem) {
                    if ($taxItem instanceof TaxItemInterface) {
                        $convertedItem = clone $taxItem;
                        $convertedItem->setAmount($this->convert($taxItem->getAmount(), $cart));

                        $convertedItemTaxesFieldCollection->add($convertedItem);
                    }
                }
            }

            $item->setConvertedTaxes($convertedItemTaxesFieldCollection);
        }

        foreach ($cart->getAdjustments() as $adjustment) {
            $convertedAdjustment = clone $adjustment;
            $convertedAdjustment->setAmount(
                $this->convert($convertedAdjustment->getAmount(true), $cart),
                $this->convert($convertedAdjustment->getAmount(false), $cart)
            );

            $cart->addConvertedAdjustment($convertedAdjustment);
        }

        $convertedTaxesFieldCollection = new Fieldcollection();

        if ($cart->getTaxes() instanceof Fieldcollection) {
            foreach ($cart->getTaxes()->getItems() as $taxItem) {
                if ($taxItem instanceof TaxItemInterface) {
                    $convertedItem = clone $taxItem;
                    $convertedItem->setAmount($this->convert($taxItem->getAmount(), $cart));

                    $convertedTaxesFieldCollection->add($convertedItem);
                }
            }
        }

        $cart->setConvertedTaxes($convertedTaxesFieldCollection);
    }

    protected function convert(?int $value, OrderInterface $cart, bool $backwards = false)
    {
        if (null === $value) {
            return 0;
        }

        if (!$cart->getBaseCurrency() instanceof CurrencyInterface) {
            return $value;
        }

        if ($backwards) {
            return $this->currencyConverter->convert(
                $value,
                $cart->getCurrency()->getIsoCode(),
                $cart->getBaseCurrency()->getIsoCode()
            );
        }

        return $this->currencyConverter->convert(
            $value,
            $cart->getBaseCurrency()->getIsoCode(),
            $cart->getCurrency()->getIsoCode()
        );
    }
}
