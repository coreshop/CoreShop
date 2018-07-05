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

namespace CoreShop\Bundle\InventoryBundle\Templating\Helper;

use CoreShop\Component\Inventory\Model\StockableInterface;

interface InventoryHelperInterface
{
    /**
     * @param StockableInterface $stockable
     *
     * @return bool
     */
    public function isStockAvailable(StockableInterface $stockable);

    /**
     * @param StockableInterface $stockable
     * @param int                $quantity
     *
     * @return bool
     */
    public function isStockSufficient(StockableInterface $stockable, $quantity);
}
