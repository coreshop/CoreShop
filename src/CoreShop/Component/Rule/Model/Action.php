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

namespace CoreShop\Component\Rule\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\SetValuesTrait;
use Doctrine\Common\Collections\Collection;
use CoreShop\Component\Rule\Model\ActionInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;

/**
 * @psalm-suppress MissingConstructor
 */
class Action implements ActionInterface
{
    use SetValuesTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var int
     */
    protected $sort;

    /**
     * @var array
     */
    protected $configuration;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getSort()
    {
        return $this->sort;
    }

    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    public function setConfiguration(array $configuration)
    {
        $this->configuration = $this->normalizeConfiguration($configuration);

        return $this;
    }

    private function normalizeConfiguration(array $configuration): array
    {
        foreach ($configuration as $key => $value) {
            if ($value instanceof ConditionInterface || $value instanceof ActionInterface) {
                continue;
            } elseif ($value instanceof Collection) {
                $configuration[$key] = $this->normalizeConfiguration($value->toArray());
            } elseif ($value instanceof ResourceInterface) {
                $configuration[$key] = $value->getId();
            } elseif (is_array($value)) {
                $configuration[$key] = $this->normalizeConfiguration($value);
            }
        }

        return $configuration;
    }
}
