<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.com/license     GNU General Public License version 3 (GPLv3)
 */

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

namespace CoreShop\Component\Resource\DataHub\Type;

use GraphQL\Type\Definition\ScalarType;

class DateTimeType extends ScalarType
{
    public string $name = 'datetime';

    public function serialize($value)
    {
        if ($value !== null) {
            return $value->getTimestamp();
        }

        return null;
    }

    public function parseValue($value)
    {
        if ($value !== null) {
            $date = new \DateTime();

            return $date->setTimestamp($value);
        }

        return null;
    }

    public function parseLiteral($valueNode, ?array $variables = null)
    {
        if ($valueNode != null && $valueNode->value !== null) {
            $date = new \DateTime();

            return $date->setTimestamp($valueNode->value);
        }

        return null;
    }
}
