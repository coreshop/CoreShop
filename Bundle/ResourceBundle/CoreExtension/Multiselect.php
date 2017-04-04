<?php

namespace CoreShop\Bundle\ResourceBundle\CoreExtension;

use Pimcore\Model;

abstract class Multiselect extends Model\Object\ClassDefinition\Data\Multiselect
{
    /**
     * @param $object
     * @param array $params
     * @return string
     */
    public function preGetData($object, $params = [])
    {
        $data = $object->{$this->getName()};

        if (is_null($data)) {
            $data = [];
        }

        return $data;
    }
}
