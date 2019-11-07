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

namespace CoreShop\Bundle\InventoryBundle\Templating\Helper;

use CoreShop\Component\Inventory\Checker\AvailabilityCheckerInterface;
use CoreShop\Component\Inventory\Model\StockableInterface;
use Symfony\Component\Templating\Helper\Helper;

class InventoryHelper extends Helper implements InventoryHelperInterface
{
    /**
     * @var AvailabilityCheckerInterface
     */
    private $checker;

    /**
     * @param AvailabilityCheckerInterface $checker
     */
    public function __construct(AvailabilityCheckerInterface $checker)
    {
        $this->checker = $checker;
    }

    /**
     * {@inheritdoc}
     */
    public function isStockAvailable(StockableInterface $stockable)
    {
        return $this->checker->isStockAvailable($stockable);
    }

    /**
     * {@inheritdoc}
     */
    public function isStockSufficient(StockableInterface $stockable, $quantity = 1)
    {
        return $this->checker->isStockSufficient($stockable, $quantity);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'coreshop_inventory';
    }
}
