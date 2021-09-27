<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Registry;

class PriorityMap implements \Iterator, \Countable
{
    const ORDER_ASC = 'asc';
    const ORDER_DESC = 'desc';

    private int $lastSequence = 0;
    private array $list = [];
    private string $order = self::ORDER_ASC;

    /**
     * Get scalar key from mixed.
     */
    private function getScalarKey($key)
    {
        if (is_object($key)) {
            return spl_object_hash($key);
        }

        return $key;
    }

    /**
     * Add new item to map.
     *
     * @param string $key      name
     * @param mixed  $value    value
     * @param int    $priority priority
     *
     * @return \stdClass
     */
    public function set(string $key, mixed $value, int $priority = 0)
    {
        $key = $this->getScalarKey($key);
        $this->list[$key] = new \stdClass();
        $this->list[$key]->value = $value;
        $this->list[$key]->priority = $priority;
        $this->list[$key]->sequence = $this->lastSequence++;

        return $this->list[$key];
    }

    /**
     * Get item from map.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        $key = $this->getScalarKey($key);

        return isset($this->list[$key]) ? $this->list[$key]->value : null;
    }

    /**
     * Check if item in map.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        $key = $this->getScalarKey($key);

        return isset($this->list[$key]);
    }

    /**
     * remove item in map.
     *
     * @param string $key
     */
    public function remove($key)
    {
        $key = $this->getScalarKey($key);

        if (isset($this->list[$key])) {
            unset($this->list[$key]);
        }
    }

    /**
     * Get list of keys.
     *
     * @return array<int, int|string>
     */
    public function getKeys()
    {
        uasort($this->list, [$this, $this->order . 'SortStrategy']);

        return array_keys($this->list);
    }

    /**
     * Get count or map.
     *
     * @return int
     */
    public function count()
    {
        return count($this->list);
    }

    /**
     * Set ASC direction of sorting.
     *
     * @return $this
     */
    public function setAscOrder()
    {
        $this->order = self::ORDER_ASC;

        return $this;
    }

    /**
     * Set DESC direction of sorting.
     *
     * @return $this
     */
    public function setDescOrder()
    {
        $this->order = self::ORDER_DESC;

        return $this;
    }

    /**
     * ASC sort strategy.
     *
     * @param \stdClass $declaration1
     * @param \stdClass $declaration2
     *
     * @return int
     */
    private function ascSortStrategy($declaration1, $declaration2)
    {
        if ($declaration1->priority === $declaration2->priority) {
            return $declaration1->sequence < $declaration2->sequence ? 1 : -1;
        }

        return $declaration1->priority > $declaration2->priority ? 1 : -1;
    }

    /**
     * DESC sort strategy.
     *
     * @param \stdClass $declaration1
     * @param \stdClass $declaration2
     *
     * @return int
     */
    private function descSortStrategy($declaration1, $declaration2)
    {
        if ($declaration1->priority === $declaration2->priority) {
            return $declaration1->sequence < $declaration2->sequence ? 1 : -1;
        }

        return $declaration1->priority < $declaration2->priority ? 1 : -1;
    }

    /**
     * Reset iterator.
     */
    public function rewind()
    {
        uasort($this->list, [$this, $this->order . 'SortStrategy']);
        reset($this->list);
    }

    /**
     * Get current item.
     *
     * @return mixed
     */
    public function current()
    {
        $item = current($this->list);

        return $item->value;
    }

    /**
     * Get current key.
     *
     * @return mixed
     */
    public function key()
    {
        return key($this->list);
    }

    /**
     * Mve iterator next.
     */
    public function next()
    {
        next($this->list);
    }

    /**
     * Check if current key is valid.
     *
     * @return bool
     */
    public function valid()
    {
        return null !== $this->key();
    }

    /**
     * Convert map to array.
     *
     * @return array
     */
    public function toArray()
    {
        return iterator_to_array($this);
    }
}
