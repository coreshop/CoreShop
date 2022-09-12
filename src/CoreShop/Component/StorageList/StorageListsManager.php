<?php
declare(strict_types=1);

namespace CoreShop\Component\StorageList;

use CoreShop\Component\StorageList\Context\StorageListContextInterface;
use CoreShop\Component\StorageList\Processor\StorageListProcessorInterface;

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
