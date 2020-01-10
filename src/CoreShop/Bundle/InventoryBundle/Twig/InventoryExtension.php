<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\InventoryBundle\Twig;

use CoreShop\Bundle\InventoryBundle\Templating\Helper\InventoryHelper;

final class InventoryExtension extends \Twig_Extension
{
    /**
     * @var InventoryHelper
     */
    private $helper;

    /**
     * @param InventoryHelper $helper
     */
    public function __construct(InventoryHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('coreshop_inventory_is_available', [$this->helper, 'isStockAvailable']),
            new \Twig_SimpleFunction('coreshop_inventory_is_sufficient', [$this->helper, 'isStockSufficient']),
        ];
    }
}
