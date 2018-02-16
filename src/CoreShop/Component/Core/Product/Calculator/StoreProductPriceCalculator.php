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

namespace CoreShop\Component\Core\Product\Calculator;

use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;
use CoreShop\Component\Product\Calculator\ProductRetailPriceCalculatorInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use Webmozart\Assert\Assert;

final class StoreProductPriceCalculator implements ProductRetailPriceCalculatorInterface
{
    /**
     * @var StoreContextInterface
     */
    protected $storeContext;

    /**
     * @param StoreContextInterface $storeContext
     */
    public function __construct(StoreContextInterface $storeContext)
    {
        $this->storeContext = $storeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getRetailPrice(ProductInterface $subject)
    {
        /**
         * @var $subject \CoreShop\Component\Core\Model\ProductInterface
         */
        Assert::isInstanceOf($subject, \CoreShop\Component\Core\Model\ProductInterface::class);

        $price = $subject->getStorePrice($this->storeContext->getStore());

        if (is_null($price)) {
            return false;
        }

        return $price;
    }
}