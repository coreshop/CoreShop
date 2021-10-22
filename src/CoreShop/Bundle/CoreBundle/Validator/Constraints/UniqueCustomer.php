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

namespace CoreShop\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class UniqueCustomer extends Constraint
{
    public string $messageEmail;
    public string $messageUsername;

    public function validatedBy(): string
    {
        return 'coreshop_unique_customer';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
