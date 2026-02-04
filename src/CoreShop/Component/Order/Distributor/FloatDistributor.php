<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\Order\Distributor;

use Webmozart\Assert\Assert;

final class FloatDistributor implements FloatDistributorInterface
{
    public function distribute(float $floatAmount, float $numberOfTargets): array
    {
        Assert::true((1 <= $numberOfTargets), 'Number of targets must be bigger than 0.');

        $sign = $floatAmount < 0 ? -1 : 1;
        $amount = abs($floatAmount);

        $intTargetAmount = (int) floor($numberOfTargets);
        $floatTargetAmount = $numberOfTargets - $intTargetAmount;

        $floatAmountOfAmount = floor($amount / $numberOfTargets * $floatTargetAmount);
        $amount -= $floatAmountOfAmount;

        $low = (int) ($amount / $intTargetAmount);
        $high = $low + 1;

        $remainder = $amount % $intTargetAmount;
        $result = [];

        for ($i = 0; $i < $remainder; ++$i) {
            $result[] = $high * $sign;
        }

        for ($i = $remainder; $i < $intTargetAmount; ++$i) {
            $result[] = $low * $sign;
        }

        $result[] = (int) $floatAmountOfAmount;

        return $result;
    }
}
