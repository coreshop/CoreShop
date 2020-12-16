<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Pimcore\BatchProcessing;

use Countable;
use Iterator;
use Pimcore\Model\DataObject;

final class DataObjectBatchListing implements Iterator, Countable
{
    /**
     * @var DataObject\Listing
     */
    private $list;

    /**
     * @var int
     */
    private $batchSize;

    /**
     * @var int
     */
    private $index = 0;

    /**
     * @var int
     */
    private $loop = 0;

    /**
     * @var int
     */
    private $currentLoopLoaded = -1;

    /**
     * @var int
     */
    private $total = 0;

    /**
     * @var array
     */
    private $items = [];
    
    /**
     * @var array
     */
    private $ids = [];

    public function __construct(DataObject\Listing $list, int $batchSize)
    {
        $this->list = $list;
        $this->batchSize = $batchSize;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->items[$this->index];
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->index++;

        if ($this->index >= $this->batchSize) {
            $this->index = 0;
            $this->loop++;

            $this->load();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return ($this->index + 1) * ($this->loop + 1);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return isset($this->items[$this->index]);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->index = 0;
        $this->loop = 0;
        $this->currentLoopLoaded = -1;

        $this->load();
    }

    public function count()
    {
        if (!$this->total) {
            $dao = $this->list->getDao();

            if (!method_exists($dao, 'getTotalCount')) {
                throw new \InvalidArgumentException(sprintf('%s listing class does not support count.',
                    get_class($this->list)));
            }

            $this->total = $dao->getTotalCount();
        }

        return $this->total;
    }

    /**
     * Load all items based on current state.
     */
    private function load()
    {
        if (null === $this->ids) {
            $dao = $this->list->getDao();

            if (!method_exists($dao, 'loadIdList')) {
                throw new \InvalidArgumentException(sprintf('%s listing class does not support loadIdList.',
                    get_class($this->list)));
            }

            $this->ids = $dao->loadIdList();
        }

        if ($this->currentLoopLoaded !== $this->loop) {
            $items = [];

            for ($i = 0; $i < $this->batchSize; $i++) {
                $idOffset = ($this->loop * $this->batchSize) + $i;

                $itemId = $this->ids[$idOffset];

                $items[$i] = DataObject::getById($itemId);
            }

            $this->items = $items;
            $this->currentLoopLoaded = $this->loop;
        }
    }
}
