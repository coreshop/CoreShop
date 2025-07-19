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

namespace CoreShop\Bundle\IndexBundle\Condition\OpenSearch;

use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Condition\DynamicRendererInterface;
use CoreShop\Component\Index\Condition\InArrayCondition;
use CoreShop\Component\Index\Condition\InCondition;
use CoreShop\Component\Index\Condition\NotInArrayCondition;
use CoreShop\Component\Index\Condition\NotInCondition;
use CoreShop\Component\Index\Worker\OpenSearchWorkerInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use Webmozart\Assert\Assert;

class InRenderer implements DynamicRendererInterface
{
    public function render(WorkerInterface $worker, ConditionInterface $condition, array $params = []): array
    {
        /**
         * @var InCondition|InArrayCondition $condition
         */
        Assert::isInstanceOfAny($condition, [InCondition::class, InArrayCondition::class]);

        if ($condition instanceof InCondition) {
            $values = $condition->getValues();
        } else {
            $values = $condition->getValue();
        }

        $fieldName = $params['mappedFieldName'] ?? $condition->getFieldName();

        if (count($values) === 0) {
            return [];
        }

        $conditionType = ($condition instanceof NotInCondition || $condition instanceof NotInArrayCondition) ? 'must_not' : 'must';

        return [
            $conditionType => [
                'terms' => [
                    $fieldName => $values,
                ],
            ],
        ];
    }

    public function supports(WorkerInterface $worker, ConditionInterface $condition): bool
    {
        return $worker instanceof OpenSearchWorkerInterface && ($condition instanceof InCondition || $condition instanceof InArrayCondition);
    }
}
