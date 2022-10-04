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

namespace CoreShop\Component\Sequence\Model;

use CoreShop\Component\Resource\Model\SetValuesTrait;

/**
 * @psalm-suppress MissingConstructor
 */
class Sequence implements SequenceInterface
{
    use SetValuesTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var int
     */
    protected $index = 0;

    public function getId()
    {
        return $this->id;
    }

    public function getIndex()
    {
        return $this->index;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return void
     */
    public function incrementIndex()
    {
        ++$this->index;
    }
}
