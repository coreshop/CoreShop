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

namespace CoreShop\Component\Pimcore\BatchProcessing;

use Iterator;
use Countable;
use Pimcore\Model\Listing\AbstractListing;
use Pimcore\Model\ModelInterface;

final class BatchListing implements Iterator, Countable
{
    private int $index = 0;
    private int $loop = 0;
    private int $total = 0;
    private array $items = [];

    public function __construct(private AbstractListing $list, private int $batchSize)
    {
        $this->list->setLimit($batchSize);
    }

    public function current(): ModelInterface
    {
        return $this->items[$this->index];
    }

    public function next(): void
    {
        $this->index++;

        if ($this->index >= $this->batchSize) {
            $this->index = 0;
            $this->loop++;

            $this->load();
        }
    }

    public function key(): int
    {
        return ($this->loop * $this->batchSize) + ($this->index + 1);
    }

    public function valid(): bool
    {
        return isset($this->items[$this->index]);
    }

    public function rewind(): void
    {
        $this->index = 0;
        $this->loop = 0;

        $this->load();
    }

    public function count(): int
    {
        if (!$this->total) {
            $dao = $this->list->getDao();

            if (!method_exists($dao, 'getTotalCount')) {
                throw new \InvalidArgumentException(sprintf('%s listing class does not support count.', $this->list::class));
            }

            $this->total = $dao->getTotalCount();
        }

        return $this->total;
    }

    /**
     * Load all items based on current state.
     */
    private function load(): void
    {
        $this->list->setOffset($this->loop * $this->batchSize);

        $dao = $this->list->getDao();

        if (!method_exists($dao, 'load')) {
            throw new \InvalidArgumentException(sprintf('%s listing class does not support load.', $this->list::class));
        }

        $this->items = $dao->load();
    }
}
