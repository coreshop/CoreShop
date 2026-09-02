<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\OrderBundle\AdminClass\Pimcore\GridColumnConfig\Operator\Factory;

use CoreShop\Bundle\OrderBundle\AdminClass\Pimcore\GridColumnConfig\Operator\OrderState;
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
