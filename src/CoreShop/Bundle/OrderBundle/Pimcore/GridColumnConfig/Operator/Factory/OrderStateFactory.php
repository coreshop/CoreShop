<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\OrderBundle\Pimcore\GridColumnConfig\Operator\Factory;

use CoreShop\Bundle\OrderBundle\Pimcore\GridColumnConfig\Operator\OrderState;
use CoreShop\Bundle\WorkflowBundle\StateManager\WorkflowStateInfoManagerInterface;
use Pimcore\DataObject\GridColumnConfig\Operator\Factory\OperatorFactoryInterface;
use Pimcore\DataObject\GridColumnConfig\Operator\OperatorInterface;

class OrderStateFactory implements OperatorFactoryInterface
{
    /**
     * @var WorkflowStateInfoManagerInterface
     */
    private $workflowManager;

    /**
     * @param WorkflowStateInfoManagerInterface $workflowManager
     */
    public function __construct(WorkflowStateInfoManagerInterface $workflowManager)
    {
        $this->workflowManager = $workflowManager;
    }

    /**
     * @param \stdClass $configElement
     * @param null      $context
     *
     * @return OperatorInterface
     */
    public function build(\stdClass $configElement, $context = null): OperatorInterface
    {
        return new OrderState($this->workflowManager, $configElement, $context);
    }
}
