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

class DoctrineDeferredBuffer
{
    protected $buffer = array();

    protected $results = array();

    protected $loaded = false;

    protected $bufferKeys = array();

    public function add($id)
    {
        if (!in_array(implode(':', $id), $this->bufferKeys)) {
            $this->bufferKeys[] = implode(':', $id);
            $this->buffer[] = $id;
        }
    }

    public function isLoaded()
    {
        return $this->loaded;
    }

    public function load($items)
    {
        $this->loaded = true;

        $this->results = $items;
    }

    public function result($id)
    {
        if (isset($this->results[$id])) {
            return $this->results[$id];
        }

        return null;

    }

    public function get()
    {
        return $this->buffer;
    }
}
