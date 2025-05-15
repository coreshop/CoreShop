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

namespace CoreShop\Component\Index\Filter;

use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\FilterConditionInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

final class NestedFilterConditionProcessor implements FilterConditionProcessorInterface
{
    public function __construct(
        private ServiceRegistryInterface $conditionProcessors,
    ) {
    }

    public function prepareValuesForRendering(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, array $currentFilter): array
    {
        $conditions = $condition->getConfiguration()['conditions'];
        $conditionParams = [];

        if (is_array($conditions)) {
            foreach ($conditions as $cond) {
                $filterProcessor = $this->conditionProcessors->get($cond->getType());

                if ($filterProcessor instanceof FilterConditionProcessorInterface) {
                    $conditionParams[] = $filterProcessor->prepareValuesForRendering(
                        $cond,
                        $filter,
                        $list,
                        $currentFilter,
                    );
                }
            }
        }

        return [
            'type' => 'nested',
            'label' => $condition->getLabel(),
            'conditions' => $conditionParams,
        ];
    }

    public function addCondition(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, array $currentFilter, ParameterBag $parameterBag, bool $isPrecondition = false): array
    {
        $conditions = $condition->getConfiguration()['conditions'];

        foreach ($conditions as $cond) {
            $filterProcessor = $this->conditionProcessors->get($cond->getType());

            if ($filterProcessor instanceof FilterConditionProcessorInterface) {
                $currentFilter = $filterProcessor->addCondition(
                    $cond,
                    $filter,
                    $list,
                    $currentFilter,
                    $parameterBag,
                    $isPrecondition,
                );
            }
        }

        return $currentFilter;
    }
}
