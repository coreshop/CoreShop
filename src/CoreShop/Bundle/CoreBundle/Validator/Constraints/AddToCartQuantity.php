<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @deprecated AddToCartQuantity has been deprecated with CoreShop 2.1.1 and will be removed with 3.0, use AddToCartMinimumQuantity instead
 */
final class AddToCartQuantity extends Constraint
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
            'Calling AddToCartQuantity has been deprecated with CoreShop 2.1.1 and will be removed with 3.0, use AddToCartMinimumQuantity instead',
            E_USER_DEPRECATED
        );
        return 'coreshop_add_to_cart_quantity';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets(): string
    {
        @trigger_error(
            'Calling AddToCartQuantity has been deprecated with CoreShop 2.1.1 and will be removed with 3.0, use AddToCartMinimumQuantity instead',
            E_USER_DEPRECATED
        );
        return self::CLASS_CONSTRAINT;
    }
}
