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
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use Webmozart\Assert\Assert;

class CategoriesConditionChecker implements ConditionCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isValid($subject, array $configuration)
    {
        Assert::isInstanceOf($subject, CartInterface::class);

        if (!$subject instanceof CartInterface) {
            return false;
        }

        foreach ($subject->getItems() as $item) {
            $product = $item->getProduct();

            if ($product instanceof ProductInterface) {
                foreach ($product->getCategories() as $category) {
                    if ($category instanceof ResourceInterface) {
                        if (in_array($category->getId(), $configuration['categories'])) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }
}
