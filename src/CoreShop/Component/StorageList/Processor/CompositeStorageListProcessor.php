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

namespace CoreShop\Component\StorageList\Processor;

use CoreShop\Component\StorageList\Model\StorageListInterface;
use Laminas\Stdlib\PriorityQueue;

final class CompositeStorageListProcessor implements StorageListProcessorInterface
{
    private PriorityQueue $cartProcessors;

    public function __construct()
    {
        $this->cartProcessors = new PriorityQueue();
    }

    public function addProcessor(StorageListProcessorInterface $cartProcessor, int $priority = 0): void
    {
        $this->cartProcessors->insert($cartProcessor, $priority);
    }

    public function process(StorageListInterface $storageList): void
    {
        foreach ($this->cartProcessors as $cartProcessor) {
            $cartProcessor->process($storageList);
        }
    }
}
