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

namespace CoreShop\Bundle\OrderBundle\Twig;

use CoreShop\Bundle\OrderBundle\Templating\Helper\OrderStateHelperInterface;

final class OrderStateExtension extends \Twig_Extension
{
    /**
     * @var OrderStateHelperInterface
     */
    private $helper;

    /**
     * @param OrderStateHelperInterface $helper
     */
    public function __construct(OrderStateHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_Filter('coreshop_order_state', [$this->helper, 'getOrderState']),
        ];
    }
}
