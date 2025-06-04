<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\InventoryBundle\Twig;

use CoreShop\Component\Inventory\Checker\AvailabilityCheckerInterface;
use CoreShop\Component\Inventory\Model\StockableInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class InventoryExtension extends AbstractExtension
{
    public function __construct(
        private AvailabilityCheckerInterface $checker,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('coreshop_inventory_is_available', [$this->checker, 'isStockAvailable']),
            new TwigFunction('coreshop_inventory_is_sufficient', [$this, 'isStockSufficient']),
        ];
    }

    public function isStockSufficient(StockableInterface $stockable, float $quantity = 1): bool
    {
        return $this->checker->isStockSufficient($stockable, $quantity);
    }
}
