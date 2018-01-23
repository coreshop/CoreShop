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

namespace CoreShop\Component\Core\Order\Processor;

use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Taxation\Collector\TaxCollectorInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;
use Pimcore\Model\DataObject\Fieldcollection;

final class CartTaxProcessor implements CartProcessorInterface
{
    /**
     * @var TaxCollectorInterface
     */
    private $taxCollector;

    /**
     * @param TaxCollectorInterface $taxCollector
     */
    public function __construct(TaxCollectorInterface $taxCollector)
    {
        $this->taxCollector = $taxCollector;
    }

    /**
     * {@inheritdoc}
     */
    public function process(CartInterface $cart)
    {
        $usedTaxes = [];

        /**
         * @var $item CartItemInterface
         */
        foreach ($cart->getItems() as $item) {
            $usedTaxes = $this->taxCollector->mergeTaxes($item->getTaxes() instanceof Fieldcollection ? $item->getTaxes()->getItems() : [], $usedTaxes);
        }

        $fieldCollection = new Fieldcollection();
        $fieldCollection->setItems($usedTaxes);

        if ($cart->getDiscountPercentage() > 0) {
            foreach ($usedTaxes as $taxItem) {
                if (!$taxItem instanceof TaxItemInterface) {
                    continue;
                }

                $taxItem->setAmount($taxItem->getAmount() - ($taxItem->getAmount() * $cart->getDiscountPercentage()));
            }
        }

        $cart->setTaxes($fieldCollection);
    }
}