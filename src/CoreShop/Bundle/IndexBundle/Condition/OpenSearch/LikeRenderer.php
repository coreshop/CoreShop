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
use CoreShop\Component\Index\Condition\LikeCondition;
use CoreShop\Component\Index\Worker\OpenSearchWorkerInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use Webmozart\Assert\Assert;

class LikeRenderer implements DynamicRendererInterface
{
    public function render(WorkerInterface $worker, ConditionInterface $condition, array $params = []): array
    {
        /**
         * @var LikeCondition $condition
         */
        Assert::isInstanceOf($condition, LikeCondition::class);

        $fieldName = $params['mappedFieldName'] ?? $condition->getFieldName();
        $value = $condition->getValue();
        $pattern = $condition->getPattern();

        if ($pattern === 'both') {
            $value = '*' . $value . '*';
        } elseif ($pattern === 'left') {
            $value = '*' . $value;
        } elseif ($pattern === 'right') {
            $value .= '*';
        }

        return [
            'filter' => [
                'wildcard' => [
                    $fieldName => $value,
                ],
            ],
        ];
    }

    public function supports(WorkerInterface $worker, ConditionInterface $condition): bool
    {
        return $worker instanceof OpenSearchWorkerInterface && $condition instanceof LikeCondition;
    }
}
