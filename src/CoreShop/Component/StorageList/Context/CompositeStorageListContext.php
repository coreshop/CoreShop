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

namespace CoreShop\Component\StorageList\Context;

use CoreShop\Component\StorageList\Model\StorageListInterface;
use Laminas\Stdlib\PriorityQueue;

class CompositeStorageListContext implements StorageListContextInterface
{
    /**
     * @var PriorityQueue|StorageListContextInterface[]
     * @psalm-var PriorityQueue<StorageListContextInterface>
     */
    protected PriorityQueue $contexts;

    public function __construct()
    {
        $this->contexts = new PriorityQueue();
    }

    public function addContext(StorageListContextInterface $context, int $priority = 0): void
    {
        $this->contexts->insert($context, $priority);
    }

    public function getStorageList(): StorageListInterface
    {
        foreach ($this->contexts as $context) {
            try {
                return $context->getStorageList();
            } catch (StorageListNotFoundException) {
                continue;
            }
        }

        throw new StorageListNotFoundException();
    }
}
