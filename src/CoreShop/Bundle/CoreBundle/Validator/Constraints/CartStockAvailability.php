<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class CartStockAvailability extends Constraint
{
    public string $message = 'coreshop.cart_item.not_sufficient_stock';

    public function validatedBy(): string
    {
        return 'coreshop_cart_stock_availability';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
