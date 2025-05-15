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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\ResourceBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class Active extends Constraint
{
    public const IS_NOT_ENABLED_ERROR = '20bc0272-8e0b-4693-89a1-feb4ceabcfac';

    public string $message = 'This selected value is not active.';
}
