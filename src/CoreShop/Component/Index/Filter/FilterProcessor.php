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

namespace CoreShop\Component\Index\Filter;

use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\FilterConditionInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class FilterProcessor implements FilterProcessorInterface
{
    public function __construct(private ServiceRegistryInterface $preConditionProcessors, private ServiceRegistryInterface $userConditionProcessors)
    {
    }

    public function processConditions(FilterInterface $filter, ListingInterface $list, ParameterBag $parameterBag): array
    {
        $currentFilter = [];
        $conditions = $filter->getConditions();
        $preConditions = $filter->getPreConditions();

        if ($filter->hasConditions()) {
            foreach ($conditions as $condition) {
                $currentFilter = $this->getConditionProcessorForCondition($condition)->addCondition($condition, $filter, $list, $currentFilter, $parameterBag, false);
            }
        }

        if ($filter->hasPreConditions()) {
            foreach ($preConditions as $condition) {
                $currentFilter = $this->getPreConditionProcessorForCondition($condition)->addCondition($condition, $filter, $list, $currentFilter, $parameterBag, true);
            }
        }

        return $currentFilter;
    }

    public function prepareConditionsForRendering(FilterInterface $filter, ListingInterface $list, $currentFilter): array
    {
        $conditions = $filter->getConditions();
        $preparedConditions = [];

        if ($filter->hasConditions()) {
            foreach ($conditions as $condition) {
                $preparedConditions[$condition->getId()] = $this->getConditionProcessorForCondition($condition)->prepareValuesForRendering($condition, $filter, $list, $currentFilter);
            }
        }

        return $preparedConditions;
    }

    private function getConditionProcessorForCondition(FilterConditionInterface $condition): FilterConditionProcessorInterface
    {
        /**
         * @var FilterConditionProcessorInterface $processor
         */
        $processor = $this->userConditionProcessors->get($condition->getType());

        return $processor;
    }

    private function getPreConditionProcessorForCondition(FilterConditionInterface $condition): FilterConditionProcessorInterface
    {
        /**
         * @var FilterConditionProcessorInterface $processor
         */
        $processor = $this->preConditionProcessors->get($condition->getType());

        return $processor;
    }
}
