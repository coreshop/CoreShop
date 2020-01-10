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

namespace CoreShop\Bundle\ResourceBundle\Serialization;

use JMS\Serializer\Context;
use JMS\Serializer\JsonSerializationVisitor;
use Pimcore\Model\Document;

class PimcoreDocumentHandler
{
    public function serializeRelation(JsonSerializationVisitor $visitor, $relation, array $type, Context $context)
    {
        if ($relation instanceof Document) {
            return $relation->getId();
        }

        return null;
    }
}
