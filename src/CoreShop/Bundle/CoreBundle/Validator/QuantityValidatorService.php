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

namespace CoreShop\Bundle\CoreBundle\Validator;

class QuantityValidatorService
{
    /**
     * @param mixed $minimumLimit
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
     */
    public function isHigherThenMaxLimit($maximumLimit, float $quantity): bool
    {
        if (!is_numeric($maximumLimit)) {
            return false;
        }

        return $quantity > $maximumLimit;
    }
}
