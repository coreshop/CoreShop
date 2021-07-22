<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Validator;

class QuantityValidatorService
{
    /**
     * @param mixed $minimumLimit
     * @param float $quantity
     *
     * @return bool
     */
    public function isLowerThenMinLimit($minimumLimit, float $quantity): bool
    {
        if (!is_numeric($minimumLimit)) {
            return false;
        }

        return $quantity < $minimumLimit;
    }

    /**
     * @param mixed $maximumLimit
     * @param float $quantity
     *
     * @return bool
     */
    public function isHigherThenMaxLimit($maximumLimit, float $quantity): bool
    {
        if (!is_numeric($maximumLimit)) {
            return false;
        }

        return $quantity > $maximumLimit;
    }
}
