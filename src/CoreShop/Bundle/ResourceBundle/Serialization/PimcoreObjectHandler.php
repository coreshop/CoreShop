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

namespace CoreShop\Bundle\ResourceBundle\Serialization;

use JMS\Serializer\Context;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Concrete;

class PimcoreObjectHandler
{
    public function serializeRelation(JsonSerializationVisitor $visitor, $relation, array $type, Context $context): ?int
    {
        if ($relation instanceof Concrete) {
            return $relation->getId();
        }

        return null;
    }

    /**
     * @return (DataObject|null)[]|DataObject|null
     *
     * @psalm-return DataObject|list<DataObject|null>|null
     */
    public function deserializeRelation(JsonDeserializationVisitor $visitor, $relation, array $type, Context $context): array|DataObject|null
    {
        $className = $type['params'][0]['name'] ?? null;

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
