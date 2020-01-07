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

use CoreShop\Bundle\CoreBundle\Validator\QuantityValidatorService;
use CoreShop\Component\StorageList\StorageListItemResolverInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @deprecated AddToCartQuantityValidator is deprecated use AddToCartMinimumQuantityValidator instead
 */
final class AddToCartQuantityValidator extends ConstraintValidator
{
    /**
     * @param QuantityValidatorService         $quantityValidatorService
     * @param StorageListItemResolverInterface $cartItemResolver
     */
    public function __construct(
        QuantityValidatorService $quantityValidatorService,
        StorageListItemResolverInterface $cartItemResolver = null
    )
    {
        @trigger_error(
            'AddToCartQuantityValidator is deprecated use AddToCartMinimumQuantityValidator instead',
            E_USER_DEPRECATED
        );
    }

    /**
     * @param mixed      $addToCartDto
     * @param Constraint $constraint
     */
    public function validate($addToCartDto, Constraint $constraint): void
    {
        @trigger_error(
            'AddToCartQuantityValidator is deprecated use AddToCartMinimumQuantityValidator instead',
            E_USER_DEPRECATED
        );
    }
}
