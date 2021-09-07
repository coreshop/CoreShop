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

final class NestedFilterConditionProcessor implements FilterConditionProcessorInterface
{
    private ServiceRegistryInterface $conditionProcessors;

    public function __construct(ServiceRegistryInterface $conditionProcessors)
    {
        $this->conditionProcessors = $conditionProcessors;
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
                        $currentFilter
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
                    $isPrecondition
                );
            }
        }

        return $currentFilter;
    }
}
