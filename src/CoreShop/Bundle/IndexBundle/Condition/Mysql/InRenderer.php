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
use CoreShop\Component\Index\Condition\InCondition;
use CoreShop\Component\Index\Condition\NotInCondition;
use CoreShop\Component\Index\Worker\MysqlWorkerInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use Webmozart\Assert\Assert;

class InRenderer extends AbstractMysqlDynamicRenderer
{
    public function render(WorkerInterface $worker, ConditionInterface $condition, array $params = [])
    {
        /**
         * @var InCondition $condition
         */
        Assert::isInstanceOf($condition, InCondition::class);

        $inValues = [];

        foreach ($condition->getValues() as $value) {
            $inValues[] = $this->quote((string) $value);
        }

        if (count($inValues) > 0) {
            $operator = 'IN';

            if ($condition instanceof NotInCondition) {
                $operator = 'NOT IN';
            }

            return sprintf(
                '%s %s (%s)',
                $this->quoteFieldName($condition->getFieldName(), $params['prefix'] ?? null),
                $operator,
                implode(',', $inValues),
            );
        }

        return '';
    }

    public function supports(WorkerInterface $worker, ConditionInterface $condition): bool
    {
        return $worker instanceof MysqlWorkerInterface && $condition instanceof InCondition;
    }
}
