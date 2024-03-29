<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Product\Rule\Action;

use CoreShop\Component\Product\Model\ProductInterface;
use Webmozart\Assert\Assert;

class DiscountPercentActionProcessor implements ProductDiscountActionProcessorInterface
{
    public function getDiscount($subject, int $price, array $context, array $configuration): int
    {
        Assert::isInstanceOf($subject, ProductInterface::class);

        return (int) round(($configuration['percent'] / 100) * $price);
    }
}
