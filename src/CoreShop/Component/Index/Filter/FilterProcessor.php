<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Index\Filter;

use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\FilterConditionInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class FilterProcessor implements FilterProcessorInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $preConditionProcessors;

    /**
     * @var ServiceRegistryInterface
     */
    private $userConditionProcessors;

    /**
     * @param ServiceRegistryInterface $preConditionProcessors
     * @param ServiceRegistryInterface $userConditionProcessors
     */
    public function __construct(
        ServiceRegistryInterface $preConditionProcessors,
        ServiceRegistryInterface $userConditionProcessors
    ) {
        $this->preConditionProcessors = $preConditionProcessors;
        $this->userConditionProcessors = $userConditionProcessors;
    }

    /**
     * {@inheritdoc}
     */
    public function processConditions(FilterInterface $filter, ListingInterface $list, ParameterBag $parameterBag)
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

    /**
     * {@inheritdoc}
     */
    public function prepareConditionsForRendering(FilterInterface $filter, ListingInterface $list, $currentFilter)
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

    /**
     * @param FilterConditionInterface $condition
     *
     * @return FilterConditionProcessorInterface
     */
    private function getConditionProcessorForCondition(FilterConditionInterface $condition)
    {
        return $this->userConditionProcessors->get($condition->getType());
    }

    /**
     * @param FilterConditionInterface $condition
     *
     * @return FilterConditionProcessorInterface
     */
    private function getPreConditionProcessorForCondition(FilterConditionInterface $condition)
    {
        return $this->preConditionProcessors->get($condition->getType());
    }
}
