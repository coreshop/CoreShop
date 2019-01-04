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

namespace CoreShop\Bundle\InventoryBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class InStock extends Constraint
{
    /**
     * @var string
     */
    public $message = 'coreshop.cart_item.not_sufficient_stock';

    /**
     * @var string
     */
    public $stockablePath = 'stockable';

    /**
     * @var string
     */
    public $quantityPath = 'quantity';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'coreshop_in_stock';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
