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

declare(strict_types=1);

namespace CoreShop\Bundle\ResourceBundle\Serialization;

use JMS\Serializer\Context;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use Pimcore\Model\Site;

class PimcoreSiteHandler
{
    public function serializeRelation(JsonSerializationVisitor $visitor, $relation, array $type, Context $context)
    {
        if ($relation instanceof Site) {
            return $relation->getId();
        }

        return null;
    }

    public function deserializeRelation(JsonDeserializationVisitor $visitor, $relation, array $type, Context $context)
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
