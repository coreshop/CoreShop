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

namespace CoreShop\Component\Order\Repository;

use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;

interface OrderDocumentRepositoryInterface extends PimcoreRepositoryInterface
{
    /**
     * @param OrderInterface $order
     *
     * @return OrderDocumentInterface[]
     */
    public function getDocuments(OrderInterface $order);

    /**
     * @param OrderInterface $order
     * @param string $state
     *
     * @return OrderDocumentInterface[]
     */
    public function getDocumentsInState(OrderInterface $order, $state);

    /**
     * @param OrderInterface $order
     * @param string $state
     *
     * @return OrderDocumentInterface[]
     */
    public function getDocumentsNotInState(OrderInterface $order, $state);
}
