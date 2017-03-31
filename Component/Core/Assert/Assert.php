<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Assert;

use Symfony\Component\Intl\Exception\UnexpectedTypeException;

/**
 * Class Assert
 * @package CoreShop\Component\Core\Assert
 */
class Assert
{
    /**
     * @param $class
     * @param $type
     * @throws UnexpectedTypeException
     */
    public static function isInstanceOf($class, $type) {
        if (!$class instanceof $type) {
            throw new UnexpectedTypeException($class, $type);
        }
    }
}