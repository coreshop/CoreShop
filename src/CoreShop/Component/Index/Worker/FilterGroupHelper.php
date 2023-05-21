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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Index\Worker;

use CoreShop\Component\Index\Filter\FilterConditionProcessorInterface;
use CoreShop\Component\Index\Interpreter\RelationInterpreterInterface;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Pimcore\Model\DataObject\Concrete;

class FilterGroupHelper implements FilterGroupHelperInterface
{
    public function __construct(
        private ServiceRegistryInterface $interpreterServiceRegistry,
    ) {
    }

    public function getGroupByValuesForFilterGroup(IndexColumnInterface $column, ListingInterface $list, string $field): array
    {
        $type = 'field';
        $returnValues = [];

        if ($column->getInterpreter()) {
            $interpreterObject = $this->interpreterServiceRegistry->get($column->getInterpreter());

            if ($interpreterObject instanceof RelationInterpreterInterface) {
                $type = 'relation';
            }
        }

        switch ($type) {
            case 'relation':
                $values = $list->getGroupByRelationValues($field);

                foreach ($values as &$id) {
                    $id = (int) $id;
                    $obj = Concrete::getById($id);

                    if ($obj) {
                        $name = $obj->getKey();

                        if (method_exists($obj, 'getName')) {
                            $name = $obj->getName();
                        }

                        $returnValues[] = [
                            'key' => $id,
                            'value' => sprintf('%s (%s)', $name, $obj->getId()),
                        ];
                    }
                }

                break;
            default:
                $rawValues = $list->getGroupByValues($field, true);
                $values = [];

                foreach ($rawValues as $v) {
                    if ($v['value'] === null) {
                        continue;
                    }

                    $explode = is_string($v['value']) ? explode(',', $v['value']) : [$v['value']];

                    foreach ($explode as $e) {
                        if (array_key_exists($e, $values)) {
                            $values[$e]['count'] += $v['count'];

                            continue;
                        }

                        $values[$e] = ['value' => $e, 'count' => $v['count']];
                    }
                }

                foreach ($values as $value) {
                    if (array_key_exists('value', $value)) {
                        $returnValues[] = [
                            'key' => $value['value'],
                            'value' => $value['value'],
                        ];
                    } else {
                        $returnValues[] = [
                            'key' => 'empty',
                            'value' => FilterConditionProcessorInterface::EMPTY_STRING,
                        ];
                    }
                }

                break;
        }

        return $returnValues;
    }
}
