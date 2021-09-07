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

declare(strict_types=1);

namespace CoreShop\Bundle\InventoryBundle\Twig;

use CoreShop\Component\Inventory\Checker\AvailabilityCheckerInterface;
use CoreShop\Component\Inventory\Model\StockableInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class InventoryExtension extends AbstractExtension
{
    private AvailabilityCheckerInterface $checker;

    public function __construct(AvailabilityCheckerInterface $checker)
    {
        $this->checker = $checker;
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
