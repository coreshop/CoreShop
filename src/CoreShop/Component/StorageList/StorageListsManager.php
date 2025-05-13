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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\StorageList;

use CoreShop\Component\StorageList\Context\StorageListContextInterface;

class StorageListsManager
{
    private array $lists = [];

    private array $managers = [];

    private array $contexts = [];

    private array $modifiers = [];

    public function addList(
        string $name,
        StorageListManagerInterface $manager,
        StorageListContextInterface $context,
        StorageListModifierInterface $modifier,
    ): void {
        $this->lists[] = $name;
        $this->managers[$name] = $manager;
        $this->contexts[$name] = $context;
        $this->modifiers[$name] = $modifier;
    }

    public function getLists(): array
    {
        return $this->lists;
    }

    public function getManager(string $name): StorageListManagerInterface
    {
        return $this->managers[$name];
    }

    public function getContext(string $name): StorageListContextInterface
    {
        return $this->contexts[$name];
    }

    public function getModifier(string $name): StorageListModifierInterface
    {
        return $this->modifiers[$name];
    }
}
