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

    /**
     * Rehydrate an action that was stored inside a JSON "configuration" column (and therefore
     * comes back as a plain array) into an ActionInterface object. Objects are returned as-is.
     *
     * @param ActionInterface|array $action
     */
    public static function denormalize($action): ActionInterface
    {
        if ($action instanceof ActionInterface) {
            return $action;
        }

        $model = new self();
        $model->setType($action['type'] ?? null);
        $model->setConfiguration($action['configuration'] ?? []);

        if (isset($action['sort'])) {
            $model->setSort($action['sort']);
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
     * Nested conditions/actions (e.g. inside a cartItemAction) are stored inside this JSON
     * "configuration" column. json_encode() cannot serialize the model objects (they end up as
     * "{}"), so convert them to plain arrays for storage. They are rehydrated on read via
     * Condition::denormalize() / self::denormalize().
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
