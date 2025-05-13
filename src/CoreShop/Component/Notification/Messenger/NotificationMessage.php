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

namespace CoreShop\Component\Notification\Messenger;

class NotificationMessage
{
    public function __construct(
        protected string $type,
        protected string $resourceType,
        protected int $resourceId,
        protected array $params,
    ) {
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @psalm-return class-string
     */
    public function getResourceType(): string
    {
        return $this->resourceType;
    }

    public function getResourceId(): int
    {
        return $this->resourceId;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}
