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

namespace CoreShop\Bundle\ResourceBundle\Serialization;

use JMS\Serializer\Context;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use Pimcore\Model\DataObject;

class PimcoreDataObjectHandler
{
    public function serializeRelation(JsonSerializationVisitor $visitor, $relation, array $type, Context $context)
    {
        if ($relation instanceof \Traversable) {
            $relation = iterator_to_array($relation);
        }

        if (is_array($relation)) {
            return array_map([$this, 'getSingleEntityRelation'], $relation);
        }

        return $this->getSingleEntityRelation($relation);
    }

    public function deserializeRelation(JsonDeserializationVisitor $visitor, $relation, array $type, Context $context)
    {
        if (!is_array($relation)) {
            return DataObject::getById($relation);
        }

        $objects = [];
        foreach ($relation as $idSet) {
            $objects[] = DataObject::getById($idSet);
        }

        return $objects;
    }

    /**
     * @param $relation
     *
     * @return array|mixed
     */
    protected function getSingleEntityRelation($relation)
    {
        return $relation->getId();
    }
}
