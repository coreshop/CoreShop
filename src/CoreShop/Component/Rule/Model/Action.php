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

namespace CoreShop\Component\Rule\Model;

use CoreShop\Component\Resource\Model\SetValuesTrait;

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

    public function getId()
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * @return static
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @return void
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @return static
     */
    public function setConfiguration(array $configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }
}
