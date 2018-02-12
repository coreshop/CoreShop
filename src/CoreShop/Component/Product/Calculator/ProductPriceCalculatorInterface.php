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

namespace CoreShop\Component\Product\Calculator;

use CoreShop\Component\Product\Model\ProductInterface;

interface ProductPriceCalculatorInterface
{
    /**
     * @param $subject
     *
     * @return mixed
     */
    public function getPrice(ProductInterface $subject);

    /**
     * @param $subject
     * @param int $retailPrice
     *
     * @return mixed
     */
    public function getDiscountPrice(ProductInterface $subject, $retailPrice);

    /**
     * @param $subject
     * @param $price
     *
     * @return mixed
     */
    public function getDiscount(ProductInterface $subject, $price);
}
