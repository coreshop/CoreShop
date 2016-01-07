<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
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
