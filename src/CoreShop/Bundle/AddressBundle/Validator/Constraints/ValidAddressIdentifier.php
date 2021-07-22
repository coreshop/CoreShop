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

namespace CoreShop\Bundle\AddressBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class ValidAddressIdentifier extends Constraint
{
    /**
     * @var string
     */
    public $message = 'Address Identifier "%address_identifier%" is not valid.';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'coreshop_address_valid_identifier';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
