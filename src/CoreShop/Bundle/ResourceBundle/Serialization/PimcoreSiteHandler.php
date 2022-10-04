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
use Pimcore\Model\Site;

class PimcoreSiteHandler
{
    public function serializeRelation(JsonSerializationVisitor $visitor, $relation, array $type, Context $context): ?int
    {
        if ($relation instanceof Site) {
            return $relation->getId();
        }

        return null;
    }

    /**
     * @return Site|Site[]|null
     *
     * @psalm-return Site|list<Site>|null
     */
    public function deserializeRelation(JsonDeserializationVisitor $visitor, $relation, array $type, Context $context): array|Site|null
    {
        if (is_array($relation)) {
            $result = [];

            foreach ($relation as $rel) {
                $obj = Site::getById($rel);

                if ($obj) {
                    $result[] = $obj;
                }
            }

            return $result;
        }

        return Site::getById($relation);
    }
}
