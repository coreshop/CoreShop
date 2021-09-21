<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Order\Distributor;

use Webmozart\Assert\Assert;

final class FloatDistributor implements FloatDistributorInterface
{
    public function distribute(float $floatAmount, float $numberOfTargets): array
    {
        Assert::true((1 <= $numberOfTargets), 'Number of targets must be bigger than 0.');

        $sign = $floatAmount < 0 ? -1 : 1;
        $amount = abs($floatAmount);

        $intTargetAmount = (int)floor($numberOfTargets);
        $floatTargetAmount = $numberOfTargets - $intTargetAmount;

        $floatAmountOfAmount = floor($amount / $numberOfTargets * $floatTargetAmount);
        $amount = $amount - $floatAmountOfAmount;

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
