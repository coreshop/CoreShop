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

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\OrderTransitions;

class OrderCreationController extends AbstractSaleCreationController
{
    /**
     * {@inheritdoc}
     */
    protected function getTransformer()
    {
        return $this->get('coreshop.order.transformer.cart_to_order');
    }

    /**
     * {@inheritdoc}
     */
    protected function afterSaleCreation(ProposalInterface $sale)
    {
        $this->get('coreshop.state_machine_applier')->apply($sale, OrderTransitions::IDENTIFIER, OrderTransitions::TRANSITION_CONFIRM);
    }

    /**
     * {@inheritdoc}
     */
    protected function getPermission()
    {
        return 'coreshop_order_create';
    }
}
