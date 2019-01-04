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

namespace CoreShop\Bundle\OrderBundle\Templating\Helper;

use CoreShop\Component\Order\Model\OrderInterface;
use Symfony\Component\Templating\Helper\HelperInterface;

interface OrderStateHelperInterface extends HelperInterface
{
    /**
     * @param OrderInterface $order
     *
     * @return mixed
     */
    public function getOrderState(OrderInterface $order);
}
