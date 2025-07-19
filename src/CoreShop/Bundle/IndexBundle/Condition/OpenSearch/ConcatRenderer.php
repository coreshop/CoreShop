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

use CoreShop\Component\Index\Condition\ConcatCondition;
use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Condition\ConditionRendererInterface;
use CoreShop\Component\Index\Condition\DynamicRendererInterface;
use CoreShop\Component\Index\Worker\OpenSearchWorkerInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use Webmozart\Assert\Assert;

class ConcatRenderer implements DynamicRendererInterface
{
    public function __construct(
        private ConditionRendererInterface $renderer,
    ) {
    }

    public function render(WorkerInterface $worker, ConditionInterface $condition, array $params = []): array
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

        return array_merge_recursive([], ...$conditions);
    }

    public function supports(WorkerInterface $worker, ConditionInterface $condition): bool
    {
        return $worker instanceof OpenSearchWorkerInterface && $condition instanceof ConcatCondition;
    }
}
