<?php

namespace CoreShop\Bundle\ConfigurationBundle\Serialization;

use JMS\Serializer\Context;
use JMS\Serializer\JsonSerializationVisitor;

class ObjectHandler
{
    /**
     * @param JsonSerializationVisitor $visitor
     * @param $value
     * @param array $type
     * @param Context $context
     * @return mixed
     */
    public function serializeRelation(JsonSerializationVisitor $visitor, $value, array $type, Context $context)
    {
        return $value;
    }
}
