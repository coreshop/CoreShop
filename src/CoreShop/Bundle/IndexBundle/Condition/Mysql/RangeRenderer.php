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

use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Condition\RangeCondition;
use CoreShop\Component\Index\Worker\MysqlWorkerInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use Webmozart\Assert\Assert;

class RangeRenderer extends AbstractMysqlDynamicRenderer
{
    public function render(WorkerInterface $worker, ConditionInterface $condition, array $params = []): string
    {
        /**
         * @var RangeCondition $condition
         */
        Assert::isInstanceOf($condition, RangeCondition::class);

        $from = $condition->getFrom();
        $to = $condition->getTo();

        return '' . $this->quoteFieldName($condition->getFieldName(), $params['prefix'] ?? null) . ' >= ' . $from . ' AND ' .
            $this->quoteFieldName($condition->getFieldName(), $params['prefix'] ?? null) . ' <= ' . $to;
    }

    public function supports(WorkerInterface $worker, ConditionInterface $condition): bool
    {
        return $worker instanceof MysqlWorkerInterface && $condition instanceof RangeCondition;
    }
}
