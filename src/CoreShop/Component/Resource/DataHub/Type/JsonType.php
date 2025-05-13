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

namespace CoreShop\Component\Resource\DataHub\Type;

use GraphQL\Type\Definition\ScalarType;

class JsonType extends ScalarType
{
    public string $name = 'json';

    public function serialize($value)
    {
        return $value;
    }

    public function parseValue($value)
    {
        return $value;
    }

    public function parseLiteral($valueNode, ?array $variables = null)
    {
        return $this->parseNodeAsList($result, '', $valueNode);
    }

    /**
     * Recursively parse through the parameter and return a flat list of path's to values.
     *
     *
     * @return mixed
     */
    private function parseNodeAsList(&$result, $currentPath, $valueNode)
    {
        foreach ($valueNode->fields as $childNode) {
            if ($childNode->kind === 'ObjectField') {
                $subPath = $currentPath;
                if ($subPath !== '') {
                    $subPath .= '.';
                }
                $subPath .= $childNode->name->value;
                if ($childNode->value->kind === 'ObjectValue') {
                    $this->parseNodeAsList($result, $subPath, $childNode->value);
                } else {
                    $result[$subPath] = [
                        'value' => $this->mapValue($childNode->value->kind, $childNode->value->value),
                        'type' => $this->mapType($childNode->value->kind),
                    ];
                }
            }
        }

        return $result;
    }

    public function mapValue($kind, $value)
    {
        if ($kind === 'BooleanValue') {
            $value = ($value ? 'true' : 'false');
        }

        return $value;
    }

    public function mapType($kind)
    {
        $type = 'text';

        if ($kind === 'BooleanValue') {
            $type = 'boolean';
        }

        return $type;
    }
}
