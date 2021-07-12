<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Product\Exception;

class NoDiscountPriceFoundException extends \Exception
{
    public function __construct($calculatorClass, \Exception $previousException = null)
    {
        parent::__construct(sprintf('Price Calculator "%s" was not able to match a valid discount price.', $calculatorClass), 0, $previousException);
    }
}
