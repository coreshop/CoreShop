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

namespace CoreShop\Component\Core\Order\Processor;

use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;
use Pimcore\Model\DataObject\Fieldcollection;

final class CartBaseProcessor implements CartProcessorInterface
{
    /**
     * @var CurrencyConverterInterface
     */
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

        foreach ([true, false] as $withTax) {
            $cart->setBaseSubtotal($this->convert($cart->getSubtotal($withTax), $cart), $withTax);
            $cart->setBaseTotal($this->convert($cart->getTotal($withTax), $cart), $withTax);
        }

        foreach ($cart->getItems() as $item) {
            foreach ([true, false] as $withTax) {

                $item->setBaseItemRetailPrice($this->convert($item->getItemRetailPrice($withTax), $cart), $withTax);
                $item->setBaseTotal($this->convert($item->getTotal($withTax), $cart), $withTax);
                $item->setBaseItemPrice($this->convert($item->getItemPrice($withTax), $cart), $withTax);
            }

            $item->setBaseItemTax($this->convert($item->getItemTax(), $cart));

            foreach ($item->getAdjustments() as $adjustment) {
                $baseAdjustment = clone $adjustment;
                $baseAdjustmentGross = $this->convert($baseAdjustment->getAmount(true), $cart);
                $baseAdjustmentNet = $this->convert($baseAdjustment->getAmount(false), $cart);

                $baseAdjustment->setAmount($baseAdjustmentGross, $baseAdjustmentNet);

                $item->addBaseAdjustment($baseAdjustment);
            }

            $baseItemTaxesFieldCollection = new Fieldcollection();

            if ($item->getTaxes() instanceof Fieldcollection) {
                foreach ($item->getTaxes()->getItems() as $taxItem) {
                    if ($taxItem instanceof TaxItemInterface) {
                        $baseItem = clone $taxItem;
                        $baseItem->setAmount($this->convert($baseItem->getAmount(), $cart));

                        $baseItemTaxesFieldCollection->add($baseItem);
                    }
                }
            }

            $item->setBaseTaxes($baseItemTaxesFieldCollection);
        }

        foreach ($cart->getAdjustments() as $adjustment) {
            $baseAdjustment = clone $adjustment;
            $baseAdjustmentGross = $this->convert($adjustment->getAmount(true), $cart);
            $baseAdjustmentNet = $this->convert($adjustment->getAmount(false), $cart);

            $baseAdjustment->setAmount($baseAdjustmentGross, $baseAdjustmentNet);

            $cart->addBaseAdjustment($baseAdjustment);
        }

        $baseTaxesFieldCollection = new Fieldcollection();

        if ($cart->getTaxes() instanceof Fieldcollection) {
            foreach ($cart->getTaxes()->getItems() as $item) {
                if ($item instanceof TaxItemInterface) {
                    $baseItem = clone $item;
                    $baseItem->setAmount($this->convert($baseItem->getAmount(), $cart));

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

        return $this->currencyConverter->convert(
            $value,
            $cart->getBaseCurrency()->getIsoCode(),
            $cart->getCurrency()->getIsoCode()
        );
    }
}
