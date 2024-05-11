<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\StorageList\Storage;

use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use Webmozart\Assert\Assert;

class SimpleStorageListStorage implements StorageListStorageInterface
{
    private bool $gotReset = true;
    private array $simple = [];

    public function hasForContext(array $context): bool
    {
        if (!isset($this->simple[$this->getKeyName($context)])) {
            return false;
        }

        return array_key_exists($this->getKeyName($context), $this->simple);
    }

    public function gotReset(): bool
    {
        return $this->gotReset;
    }

    public function getForContext(array $context): ?StorageListInterface
    {
        if (!isset($this->simple[$this->getKeyName($context)])) {
            return null;
        }

        $this->gotReset = false;
        if ($this->hasForContext($context)) {
            return $this->simple[$this->getKeyName($context)];
        }

        return null;
    }

    public function setForContext(array $context, StorageListInterface $storageList): void
    {
        $this->gotReset = true;
        $this->simple[$this->getKeyName($context)] = $storageList;
    }

    public function removeForContext(array $context): void
    {
        unset($this->simple[$this->getKeyName($context)]);
    }

    private function getKeyName(array $context)
    {
        Assert::keyExists($context, 'store');

        /**
         * @var StoreInterface $store
         */
        $store = $context['store'];

        return sprintf('%s', $store->getId());
    }
}