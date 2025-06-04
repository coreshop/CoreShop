<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\OrderBundle\Pimcore\GridColumnConfig\Operator\Factory;

use CoreShop\Bundle\OrderBundle\Pimcore\GridColumnConfig\Operator\OrderState;
use CoreShop\Bundle\WorkflowBundle\StateManager\WorkflowStateInfoManagerInterface;
use Pimcore\Bundle\AdminBundle\DataObject\GridColumnConfig\Operator\Factory\OperatorFactoryInterface;

class OrderStateFactory implements OperatorFactoryInterface
{
    public function __construct(
        private WorkflowStateInfoManagerInterface $workflowManager,
    ) {
    }

    public function build(\stdClass $configElement, array $context = []): OrderState
    {
        return new OrderState($this->workflowManager, $configElement, $context);
    }
}
