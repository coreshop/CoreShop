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

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;

final class CartDiscountTaxProcessor implements CartProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(CartInterface $cart)
    {
        $fieldCollection = $cart->getTaxes();

        if ($cart->getDiscountPercentage() > 0) {
            foreach ($fieldCollection->getItems() as $taxItem) {
                if (!$taxItem instanceof TaxItemInterface) {
                    continue;
                }

                $taxItem->setAmount($taxItem->getAmount() - ($taxItem->getAmount() * $cart->getDiscountPercentage()));
            }
        }

        $cart->setTaxes($fieldCollection);
    }
}