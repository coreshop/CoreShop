<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace Pimcore\Model\Object\ClassDefinition\Data;

use Pimcore\Model;

/**
 * Class CoreShopShopMultiselect
 * @package Pimcore\Model\Object\ClassDefinition\Data
 */
class CoreShopMultiselect extends Model\Object\ClassDefinition\Data\Multiselect
{
    /**
     * @param $object
     * @param array $params
     * @return string
     */
    public function preGetData($object, $params = [])
    {
        $data = $object->{$this->getName()};

        if(is_null($data)) {
            $data = [];
        }

        return $data;
    }
}
