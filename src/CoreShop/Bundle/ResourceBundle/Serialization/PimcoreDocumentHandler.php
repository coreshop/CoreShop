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

namespace CoreShop\Bundle\ResourceBundle\Serialization;

use JMS\Serializer\Context;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use Pimcore\Model\Document;

class PimcoreDocumentHandler
{
    public function serializeRelation(JsonSerializationVisitor $visitor, $relation, array $type, Context $context): ?int
    {
        if ($relation instanceof Document) {
            return $relation->getId();
        }

        return null;
    }

    public function deserializeRelation(JsonDeserializationVisitor $visitor, $relation, array $type, Context $context)
    {
        $className = $type['params'][0]['name'] ?? null;

        if (is_array($relation)) {
            $result = [];

            foreach ($relation as $rel) {
                $obj = Document::getById($rel);

                if ($obj instanceof $className) {
                    $result[] = $obj;
                }
            }

            return $result;
        }

        $obj = Document::getById($relation);

        return $obj instanceof $className ? $obj : null;
    }
}
