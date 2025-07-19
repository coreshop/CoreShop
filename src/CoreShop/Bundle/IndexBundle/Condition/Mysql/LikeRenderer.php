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
use CoreShop\Component\Index\Condition\InArrayCondition;
use CoreShop\Component\Index\Condition\LikeCondition;
use CoreShop\Component\Index\Condition\NotInArrayCondition;
use CoreShop\Component\Index\Condition\NotLikeCondition;
use CoreShop\Component\Index\Worker\MysqlWorkerInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use Webmozart\Assert\Assert;

class LikeRenderer extends AbstractMysqlDynamicRenderer
{
    public function render(WorkerInterface $worker, ConditionInterface $condition, array $params = [])
    {
        if ($condition instanceof InArrayCondition) {
            $value = sprintf('%1$s%2$s%1$s', ',', implode(',', $condition->getValue()));
            $pattern = 'both';
        } else {
            /**
             * @var LikeCondition $condition
             */
            Assert::isInstanceOf($condition, LikeCondition::class);

            $value = $condition->getValue();
            $pattern = $condition->getPattern();
        }
        $operator = 'LIKE';
        $patternValue = '';

        switch ($pattern) {
            case 'left':
                $patternValue = '%' . $value;

                break;
            case 'right':
                $patternValue = $value . '%';

                break;
            case 'both':
                $patternValue = '%' . $value . '%';

                break;
        }

        if ($condition instanceof NotLikeCondition || $condition instanceof NotInArrayCondition) {
            $operator = 'NOT LIKE';
        }

        return sprintf(
            '%s %s %s',
            $this->quoteFieldName($condition->getFieldName(), $params['prefix'] ?? null),
            $operator,
            $this->quote($patternValue),
        );
    }

    public function supports(WorkerInterface $worker, ConditionInterface $condition): bool
    {
        return $worker instanceof MysqlWorkerInterface && ($condition instanceof LikeCondition || $condition instanceof InArrayCondition);
    }
}
