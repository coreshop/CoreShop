<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Resource\DataHub\Type;

use GraphQL\Type\Definition\ScalarType;

class JsonType extends ScalarType
{
    public $name = 'json';

    /**
     * {@inheritdoc}
     */
    public function serialize($value)
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function parseValue($value)
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function parseLiteral($valueNode, ?array $variables = null)
    {
        return $this->parseNodeAsList($result, '', $valueNode);
    }

    /**
     * Recursively parse through the parameter and return a flat list of path's to values.
     *
     * @param $result
     * @param $currentPath
     * @param $valueNode
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
                    $result[$subPath] = array(
                        'value' => $this->mapValue($childNode->value->kind, $childNode->value->value),
                        'type' => $this->mapType($childNode->value->kind),
                    );
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
