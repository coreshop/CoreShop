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

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class CartMaximumQuantity extends Constraint
{
    /**
     * @var string
     */
    public $messageAboveMaximum;

    /**
     * {@inheritdoc}
     */
    public function validatedBy(): string
    {
        return 'coreshop_cart_maximum_quantity';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
