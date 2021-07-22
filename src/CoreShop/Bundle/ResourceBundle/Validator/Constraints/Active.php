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

namespace CoreShop\Bundle\ResourceBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class Active extends Constraint
{
    const IS_NOT_ENABLED_ERROR = '20bc0272-8e0b-4693-89a1-feb4ceabcfac';

    protected static $errorNames = array(
        self::IS_NOT_ENABLED_ERROR => 'IS_NOT_ENABLED_ERROR',
    );

    public $message = 'This selected value is not active.';
}
