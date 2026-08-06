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

use CoreShop\Component\Resource\Model\SetValuesTrait;

/**
 * @psalm-suppress MissingConstructor
 */
class Condition implements ConditionInterface
{
    use SetValuesTrait;

    /**
     * @var int|null
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

    /**
     * Rehydrate a condition that was stored inside a JSON "configuration" column (and therefore
     * comes back as a plain array) into a ConditionInterface object. Objects are returned as-is.
     *
     * @param ConditionInterface|array $condition
     */
    public static function denormalize($condition): ConditionInterface
    {
        if ($condition instanceof ConditionInterface) {
            return $condition;
        }

        $model = new static();
        $model->setType($condition['type'] ?? null);
        $model->setConfiguration($condition['configuration'] ?? []);

        if (isset($condition['sort'])) {
            $model->setSort($condition['sort']);
        }

        return $model;
    }

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

    /**
     * Nested conditions/actions (e.g. inside a "nested" bracket condition or a cartItemAction) are
     * stored inside this JSON "configuration" column. json_encode() cannot serialize the model
     * objects (they end up as "{}"), so convert them to plain arrays for storage. They are
     * rehydrated on read via self::denormalize() / Action::denormalize().
     */
    private function normalizeConfiguration(array $configuration): array
    {
        foreach ($configuration as $key => $value) {
            if ($value instanceof ConditionInterface || $value instanceof ActionInterface) {
                $configuration[$key] = [
                    'id' => $value->getId(),
                    'type' => $value->getType(),
                    'sort' => $value->getSort(),
                    'configuration' => $value->getConfiguration(),
                ];
            } elseif (is_array($value)) {
                $configuration[$key] = $this->normalizeConfiguration($value);
            }
        }

        return $configuration;
    }
}
