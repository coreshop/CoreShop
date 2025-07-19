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
use CoreShop\Component\Index\Condition\RangeCondition;
use CoreShop\Component\Index\Worker\OpenSearchWorkerInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use Webmozart\Assert\Assert;

class RangeRenderer implements DynamicRendererInterface
{
    public function render(WorkerInterface $worker, ConditionInterface $condition, array $params = []): array
    {
        /**
         * @var RangeCondition $condition
         */
        Assert::isInstanceOf($condition, RangeCondition::class);

        $fieldName = $params['mappedFieldName'] ?? $condition->getFieldName();
        $from = $condition->getFrom();
        $to = $condition->getTo();

        return [
            'filter' => [
                'range' => [
                    $fieldName => [
                        'gte' => $from,
                        'lte' => $to,
                    ],
                ],
            ],
        ];
    }

    public function supports(WorkerInterface $worker, ConditionInterface $condition): bool
    {
        return $worker instanceof OpenSearchWorkerInterface && $condition instanceof RangeCondition;
    }
}
