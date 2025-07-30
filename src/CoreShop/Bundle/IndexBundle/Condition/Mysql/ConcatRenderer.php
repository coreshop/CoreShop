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

namespace CoreShop\Bundle\IndexBundle\Condition\Mysql;

use CoreShop\Component\Index\Condition\ConcatCondition;
use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Condition\ConditionRendererInterface;
use CoreShop\Component\Index\Worker\MysqlWorkerInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use Doctrine\DBAL\Connection;
use Webmozart\Assert\Assert;

class ConcatRenderer extends AbstractMysqlDynamicRenderer
{
    public function __construct(
        Connection $connection,
        private ConditionRendererInterface $renderer,
    ) {
        parent::__construct($connection);
    }

    public function render(WorkerInterface $worker, ConditionInterface $condition, array $params = []): string
    {
        /**
         * @var ConcatCondition $condition
         */
        Assert::isInstanceOf($condition, ConcatCondition::class);

        $conditions = [];

        foreach ($condition->getConditions() as $subCondition) {
            /**
             * @var ConditionInterface $subCondition
             */
            Assert::isInstanceOf($subCondition, ConditionInterface::class);

            $conditions[] = $this->renderer->render($worker, $subCondition, $params);
        }

        if (count($conditions) > 0) {
            return '(' . implode(' ' . trim($condition->getOperator()) . ' ', $conditions) . ')';
        }

        return '';
    }

    public function supports(WorkerInterface $worker, ConditionInterface $condition): bool
    {
        return $worker instanceof MysqlWorkerInterface && $condition instanceof ConcatCondition;
    }
}
