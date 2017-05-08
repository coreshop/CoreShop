<?php

namespace CoreShop\Component\Index\Interpreter;

use CoreShop\Component\Index\Model\IndexColumnInterface;
use Pimcore\Model\Object\AbstractObject;

class ObjectInterpreter implements RelationInterpreterInterface
{
    /**
     * {@inheritdoc}
     */
    public function interpret($value, IndexColumnInterface $config = null)
    {
        $result = [];

        if (is_array($value)) {
            foreach ($value as $v) {
                if ($v instanceof AbstractObject) {
                    $result[] = [
                        'dest' => $v->getId(),
                        'type' => 'object',
                    ];
                }
            }
        } elseif ($value instanceof AbstractObject) {
            $result[] = [
                'dest' => $value->getId(),
                'type' => 'object',
            ];
        }

        return $result;
    }
}
