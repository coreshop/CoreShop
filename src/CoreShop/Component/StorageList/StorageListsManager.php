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

namespace CoreShop\Component\StorageList;

use CoreShop\Component\StorageList\Context\StorageListContextInterface;
use CoreShop\Component\StorageList\Processor\StorageListProcessorInterface;

class StorageListsManager
{
    private array $lists = [];
    private array $managers = [];
    private array $processors = [];
    private array $contexts = [];
    private array $modifiers = [];

    public function addList(
        string $name,
        StorageListManagerInterface $manager,
        StorageListContextInterface $context,
        StorageListProcessorInterface $processor,
        StorageListModifierInterface $modifier,
    ): void {
        $this->lists[] = $name;
        $this->managers[$name] = $manager;
        $this->contexts[$name] = $context;
        $this->processors[$name] = $processor;
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

    public function getProcessor(string $name): StorageListProcessorInterface
    {
        return $this->processors[$name];
    }

    public function getModifier(string $name): StorageListModifierInterface
    {
        return $this->modifiers[$name];
    }
}
