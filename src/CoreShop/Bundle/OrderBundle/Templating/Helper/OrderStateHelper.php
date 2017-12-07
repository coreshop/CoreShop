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

namespace CoreShop\Bundle\OrderBundle\Templating\Helper;

use CoreShop\Bundle\OrderBundle\Workflow\WorkflowManager;
use CoreShop\Component\Order\Model\OrderInterface;
use Symfony\Component\Templating\Helper\Helper;

class OrderStateHelper extends Helper implements OrderStateHelperInterface
{
    /**
     * @var WorkflowManager
     */
    private $workflowManager;

    /**
     * @param WorkflowManager $workflowManager
     */
    public function __construct(WorkflowManager $workflowManager)
    {
        $this->workflowManager = $workflowManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderState(OrderInterface $order)
    {
        return $this->workflowManager->getCurrentState($order);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'coreshop_order_state';
    }
}
