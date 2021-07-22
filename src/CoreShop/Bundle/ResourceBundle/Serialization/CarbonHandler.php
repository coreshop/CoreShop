<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ResourceBundle\Serialization;

use Carbon\Carbon;
use JMS\Serializer\Context;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

class CarbonHandler
{
    public function serializeRelation(JsonSerializationVisitor $visitor, $value, array $type, Context $context)
    {
        if ($value instanceof Carbon) {
            return $value->getTimestamp();
        }

        return $value;
    }

    public function deserializeRelation(JsonDeserializationVisitor $visitor, $value, array $type, Context $context)
    {
        return Carbon::createFromTimestamp($value);
    }
}
