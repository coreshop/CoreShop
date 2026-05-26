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

final class ProportionalIntegerDistributor implements ProportionalIntegerDistributorInterface
{
    public function distribute(array $integers, int $amount): array
    {
        Assert::allInteger($integers);

        $total = array_sum($integers);
        $distributedAmounts = [];

        foreach ($integers as $element) {
            $distributedAmounts[] = (int) round(($element * $amount) / $total, 0, \PHP_ROUND_HALF_DOWN);
        }

        $missingAmount = $amount - array_sum($distributedAmounts);
        for ($i = 0, $iMax = abs($missingAmount); $i < $iMax; ++$i) {
            /** @psalm-suppress InvalidArrayOffset — $i is always >= 0, abs() never returns negative */
            $distributedAmounts[$i] += $missingAmount >= 0 ? 1 : -1;
        }

        return $distributedAmounts;
    }
}
