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

namespace CoreShop\Bundle\CoreBundle\Validator;

class QuantityValidatorService
{
    /**
     * @param mixed $minimumLimit
     * @param int   $quantity
     *
     * @return bool
     */
    public function isLowerThenMinLimit($minimumLimit, $quantity)
    {
        if (!is_numeric($minimumLimit)) {
            return false;
        }

        return $quantity < $minimumLimit;
    }

    /**
     * @param mixed $maximumLimit
     * @param int $quantity
     *
     * @return bool
     */
    public function isHigherThenMaxLimit($maximumLimit, $quantity)
    {
        if (!is_numeric($maximumLimit)) {
            return false;
        }

        return $quantity > $maximumLimit;
    }
}
