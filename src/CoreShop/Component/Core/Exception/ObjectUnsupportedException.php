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

namespace CoreShop\Component\Core\Exception;

class ObjectUnsupportedException extends \Exception
{
    /**
     * ObjectUnsupportedException constructor.
     *
     * @param string $function
     * @param int $class
     */
    public function __construct($function, $class)
    {
        parent::__construct(__FUNCTION__.' is not supported for '.$class.'. This Method needs to implemented by Pimcore Object Class. '.(get_called_class()));
    }
}
