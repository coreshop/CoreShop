<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Order\OrderList;

use Pimcore\Model\DataObject;

interface OrderListBulkInterface
{
    const SALE_TYPE_ORDER = 'order';

    const SALE_TYPE_QUOTE = 'quote';

    /**
     * The name of bulk action.
     * This value will be translated via backend translator,
     * so it's good practice to choose a symfony standard translation keys like "coreshop.order_bulk.your_bulk_name"
     * @return string
     */
    public function getName();

    /**
     * @param array $processIds
     * @return DataObject\Listing
     */
    public function apply(array $processIds);

    /**
     * Define if filter for current sale type.
     *
     * @param string $saleType
     * @return bool
     */
    public function typeIsValid($saleType = self::SALE_TYPE_ORDER);
}