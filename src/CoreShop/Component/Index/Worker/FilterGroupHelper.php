<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
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
    /**
     * @var ServiceRegistryInterface
     */
    private $interpreterServiceRegistry;

    /**
     * @param ServiceRegistryInterface $interpreterServiceRegistry
     */
    public function __construct(ServiceRegistryInterface $interpreterServiceRegistry)
    {
        $this->interpreterServiceRegistry = $interpreterServiceRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupByValuesForFilterGroup(IndexColumnInterface $column, ListingInterface $list, $field)
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
                    $id = intval($id);
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
                $values = $list->getGroupByValues($field);

                foreach ($values as $value) {
                    if ($value) {
                        $returnValues[] = [
                            'key' => $value,
                            'value' => $value,
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
