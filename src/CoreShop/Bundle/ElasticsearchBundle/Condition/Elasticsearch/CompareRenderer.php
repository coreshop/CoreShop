<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\ElasticsearchBundle\Condition\Elasticsearch;

use CoreShop\Bundle\ElasticsearchBundle\Worker\ElasticsearchWorker;
use CoreShop\Component\Index\Condition\CompareCondition;
use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use Webmozart\Assert\Assert;

class CompareRenderer extends AbstractElasticsearchDynamicRenderer
{
    public function render(WorkerInterface $worker, ConditionInterface $condition, string $prefix = null): array
    {
        /**
         * @var CompareCondition $condition
         */
        Assert::isInstanceOf($condition, CompareCondition::class);

        $value = $condition->getValue();
        $operator = $condition->getOperator();

        //TODO see what to do here
        if ($value === "true") {
            $value = true;
        }

        if ($operator === "=" || $operator === "!=") {
            if ($operator === "!=") {
                $rendered = ["not" =>
                    [
                        "match" => [
                            $condition->getFieldName() => $value
                        ]
                    ]
                ];
            } else {
                $rendered = ["match" => [
                    $condition->getFieldName() => $value
                ]];
            }
        } else {
            $map = [
                ">" => "gt",
                ">=" => "gte",
                "<" => "lt",
                "<=" => "lte"
            ];

            if (array_key_exists($operator, $map)) {
                $rendered = ["range" => [
                    $condition->getFieldName() => [
                        $map[$operator] => $value
                    ]
                ]];
            } else {
                throw new \Exception($operator . " is not supported for compare method");
            }
        }

        return $rendered;
    }

    public function supports(WorkerInterface $worker, ConditionInterface $condition): bool
    {
        return $worker instanceof ElasticsearchWorker && $condition instanceof CompareCondition;
    }
}
