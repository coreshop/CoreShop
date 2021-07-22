<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

interface OrderDocumentItemInterface extends ResourceInterface, PimcoreModelInterface
{
    /**
     * @return OrderInterface
     */
    public function getDocument();

    /**
     * @return OrderItemInterface
     */
    public function getOrderItem();

    /**
     * @param OrderItemInterface $orderItem
     */
    public function setOrderItem($orderItem);

    /**
     * @return int
     */
    public function getQuantity();

    /**
     * @param int $amount
     */
    public function setQuantity($amount);
}
