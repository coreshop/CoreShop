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

namespace CoreShop\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * TODO 3.0.0: uncomment final
 *
 * @final DO NOT EXTEND, THIS IS TEMPORARY NOT FINAL
 */
/*final*/ class AddToCartMinimumQuantity extends Constraint
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
        return 'coreshop_add_to_cart_minimum_quantity';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
