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

declare(strict_types=1);

namespace CoreShop\Component\Pimcore\BatchProcessing;

use Iterator;
use Countable;
use Pimcore\Model\Listing\AbstractListing;

final class BatchListing implements Iterator, Countable
{
    /**
     * @var AbstractListing
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
    private $total = 0;

    /**
     * @var array
     */
    private $items = [];

    public function __construct(AbstractListing $list, int $batchSize)
    {
        $this->list = $list;
        $this->batchSize = $batchSize;

        $this->list->setLimit($batchSize);
    }

    public function current()
    {
        return $this->items[$this->index];
    }

    public function next()
    {
        $this->index++;

        if ($this->index >= $this->batchSize) {
            $this->index = 0;
            $this->loop++;

            $this->load();
        }
    }

    public function key()
    {
        return ($this->index + 1) * ($this->loop + 1);
    }

    public function valid()
    {
        return isset($this->items[$this->index]);
    }

    public function rewind()
    {
        $this->index = 0;
        $this->loop = 0;

        $this->load();
    }

    public function count()
    {
        if (!$this->total) {
            $dao = $this->list->getDao();

            if (!method_exists($dao, 'getTotalCount')) {
                throw new \InvalidArgumentException(sprintf('%s listing class does not support count.', get_class($this->list)));
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
        $this->list->setOffset($this->loop * $this->batchSize);

        $dao = $this->list->getDao();

        if (!method_exists($dao, 'load')) {
            throw new \InvalidArgumentException(sprintf('%s listing class does not support load.', get_class($this->list)));
        }

        $this->items = $dao->load();
    }
}
