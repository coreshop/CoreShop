<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */

namespace Pimcore\Model\Object\ClassDefinition\Data;

use Pimcore\Model;
use CoreShop\Model\Carrier;

class CoreShopSelect extends Model\Object\ClassDefinition\Data\Select
{
    public function isEmpty($data)
    {
        return !$data;
    }

    public function getDataForSearchIndex($object)
    {
        if($object instanceof Model\Object\AbstractObject)
            return $object->getId();

        return parent::getDataForSearchIndex($object);
    }
}
