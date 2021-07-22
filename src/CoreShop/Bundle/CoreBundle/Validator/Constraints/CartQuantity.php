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

namespace CoreShop\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @deprecated CartQuantity is deprecated use CartMinimumQuantity instead
 */
final class CartQuantity extends Constraint
{
    /**
     * @var string
     */
    public $messageBelowMinimum;

    /**
     * {@inheritdoc}
     */
    public function validatedBy(): string
    {
        @trigger_error(
            'Calling CartQuantity is deprecated use CartMinimumQuantity instead',
            E_USER_DEPRECATED
        );
        return 'coreshop_cart_quantity';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets(): string
    {
        @trigger_error(
            'Calling CartQuantity is deprecated use CartMinimumQuantity instead',
            E_USER_DEPRECATED
        );
        return self::CLASS_CONSTRAINT;
    }
}
