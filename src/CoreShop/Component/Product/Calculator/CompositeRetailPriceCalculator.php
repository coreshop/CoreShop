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

namespace CoreShop\Component\Product\Calculator;

use CoreShop\Component\Product\Exception\NoRetailPriceFoundException;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;

class CompositeRetailPriceCalculator implements ProductRetailPriceCalculatorInterface
{
    protected $retailPriceCalculator;

    public function __construct(PrioritizedServiceRegistryInterface $retailPriceCalculator)
    {
        $this->retailPriceCalculator = $retailPriceCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function getRetailPrice(ProductInterface $subject, array $context): int
    {
        $price = null;

        /**
         * @var ProductRetailPriceCalculatorInterface $calculator
         */
        foreach ($this->retailPriceCalculator->all() as $calculator) {
            try {
                $actionPrice = $calculator->getRetailPrice($subject, $context);
                $price = $actionPrice;
            } catch (NoRetailPriceFoundException $exception) {
            }
        }

        if (null === $price) {
            throw new NoRetailPriceFoundException(__CLASS__);
        }

        return $price;
    }
}
