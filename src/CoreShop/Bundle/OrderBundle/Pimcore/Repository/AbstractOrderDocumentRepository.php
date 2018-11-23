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

namespace CoreShop\Bundle\OrderBundle\Pimcore\Repository;

use CoreShop\Bundle\ResourceBundle\Pimcore\PimcoreRepository;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Repository\OrderDocumentRepositoryInterface;

abstract class AbstractOrderDocumentRepository extends PimcoreRepository implements OrderDocumentRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDocuments(OrderInterface $order)
    {
        return $this->findBy(['order__id' => $order->getId()], [['key' => 'o_id', 'direction' => 'DESC']]);
    }

    /**
     * {@inheritdoc}
     */
    public function getDocumentsInState(OrderInterface $order, $state)
    {
        $list = $this->getList();
        $list->setCondition('order__id = ? AND state = ?', [$order->getId(), $state]);

        return $list->getObjects();
    }

    /**
     * {@inheritdoc}
     */
    public function getDocumentsNotInState(OrderInterface $order, $state)
    {
        $list = $this->getList();
        $list->setCondition('order__id = ? AND state <> ?', [$order->getId(), $state]);

        return $list->getObjects();
    }
}
