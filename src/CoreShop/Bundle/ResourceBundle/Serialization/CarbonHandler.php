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

use Carbon\Carbon;
use JMS\Serializer\Context;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

class CarbonHandler
{
    public function serializeRelation(JsonSerializationVisitor $visitor, $value, array $type, Context $context): int
    {
        if ($value instanceof Carbon) {
            return $value->getTimestamp();
        }

        return $value;
    }

    public function deserializeRelation(JsonDeserializationVisitor $visitor, $value, array $type, Context $context): Carbon
    {
        return Carbon::createFromTimestamp($value);
    }
}
