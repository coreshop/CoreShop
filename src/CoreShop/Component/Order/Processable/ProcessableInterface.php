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

namespace CoreShop\Component\Order\Processable;

use CoreShop\Component\Order\Model\OrderInterface;

interface ProcessableInterface
{
    /**
     * @param OrderInterface $order
     *
     * @return mixed
     */
    public function getProcessableItems(OrderInterface $order);

    /**
     * @param OrderInterface $order
     *
     * @return mixed
     */
    public function getProcessedItems(OrderInterface $order);

    /**
     * @param OrderInterface $order
     *
     * @return bool
     */
    public function isFullyProcessed(OrderInterface $order);

    /**
     * @param OrderInterface $order
     *
     * @return bool
     */
    public function isProcessable(OrderInterface $order);
}
