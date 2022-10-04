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

namespace CoreShop\Component\Notification\Model;

use CoreShop\Component\Rule\Model\RuleTrait;

/**
 * @psalm-suppress MissingConstructor
 */
class NotificationRule implements NotificationRuleInterface
{
    use RuleTrait;

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

    public function getId()
    {
        return $this->id;
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
}
