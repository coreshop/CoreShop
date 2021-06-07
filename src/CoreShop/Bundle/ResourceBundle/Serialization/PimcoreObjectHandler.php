<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\ResourceBundle\Serialization;

use JMS\Serializer\Context;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Concrete;

class PimcoreObjectHandler
{
    public function serializeRelation(JsonSerializationVisitor $visitor, $relation, array $type, Context $context)
    {
        if ($relation instanceof Concrete) {
            return $relation->getId();
        }

        return null;
    }

    public function deserializeRelation(JsonDeserializationVisitor $visitor, $relation, array $type, Context $context)
    {
        $className = isset($type['params'][0]['name']) ? $type['params'][0]['name'] : null;

        if (is_array($relation)) {
            $result = [];

            foreach ($relation as $rel) {
                $obj = DataObject::getById($rel);

                if ($obj instanceof $className) {
                    $result[] = $obj;
                }
            }

            return $result;
        }

        $obj = DataObject::getById($relation);

        return $obj instanceof $className ? $obj : null;
    }
}
