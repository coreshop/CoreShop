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

namespace CoreShop\Component\Inventory\Checker;

use CoreShop\Component\Inventory\Model\StockableInterface;

final class AvailabilityChecker implements AvailabilityCheckerInterface
{
    public function isStockAvailable(StockableInterface $stockable): bool
    {
        return $this->isStockSufficient($stockable, 1);
    }

    public function isStockSufficient(StockableInterface $stockable, float $quantity): bool
    {
        return !$stockable->getIsTracked() || $quantity <= ($stockable->getOnHand() - $stockable->getOnHold());
    }
}
