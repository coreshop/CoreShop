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
use CoreShop\Component\Index\Condition\IsNotNullCondition;
use CoreShop\Component\Index\Condition\IsNullCondition;
use CoreShop\Component\Index\Worker\OpenSearchWorkerInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use Webmozart\Assert\Assert;

class IsNullRenderer implements DynamicRendererInterface
{
    public function render(WorkerInterface $worker, ConditionInterface $condition, array $params = []): array
    {
        /**
         * @var IsNullCondition $condition
         */
        Assert::isInstanceOf($condition, IsNullCondition::class);

        $fieldName = $params['mappedFieldName'] ?? $condition->getFieldName();
        $conditionType = $condition instanceof IsNotNullCondition ? 'must' : 'must_not';

        return [
            $conditionType => [
                'exists' => [
                    'field' => $fieldName,
                ],
            ],
        ];
    }

    public function supports(WorkerInterface $worker, ConditionInterface $condition): bool
    {
        return $worker instanceof OpenSearchWorkerInterface && $condition instanceof IsNullCondition;
    }
}
