<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\StorageList\Context;

use CoreShop\Component\Pimcore\PriorityQueue;
use CoreShop\Component\StorageList\Model\StorageListInterface;

class CompositeStorageListContext implements StorageListContextInterface
{
    /**
     * @var PriorityQueue|StorageListContextInterface[]
     *
     * @psalm-var PriorityQueue<StorageListContextInterface>
     */
    protected PriorityQueue $contexts;

    public function __construct(
        ) {
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
