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

namespace CoreShop\Bundle\CoreBundle\Cart\Rule\Condition;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use Webmozart\Assert\Assert;

class ProductsConditionChecker implements ConditionCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isValid($subject, array $configuration)
    {
        /**
         * @var $subject CartInterface
         */
        Assert::isInstanceOf($subject, CartInterface::class);

        foreach ($subject->getItems() as $item) {
            $product = $item->getProduct();

            if ($product instanceof ProductInterface) {
                if (in_array($product->getId(), $configuration['products'])) {
                    return true;
                }
            }
        }

        return false;
    }
}
