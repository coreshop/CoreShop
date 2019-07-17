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

namespace CoreShop\Component\Resource\DataHub;

class GraphEntity
{
    private $data;

    private $object;

    public function __construct($data, $object)
    {
        $this->data = $data;
        $this->object = $object;
    }

    public function getDataValue($key)
    {
        return $this->data[$key];
    }

    public function getObject()
    {
        return $this->object;
    }

    public function get($key)
    {
        $methodName = 'get'.$key;

        if (method_exists($this->object, $methodName)) {
            $value = $this->object->$methodName();
        } else {

            if (method_exists($this->object, 'get')) {
                $value = $this->object->get($key);
            } else {
                $value = $this->object->$key;
            }

        }

        return $value;
    }

    public function getByMethod($methodName)
    {
        return $this->object->$methodName();
    }
}
