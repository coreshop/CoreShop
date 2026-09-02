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

namespace CoreShop\Component\Index\Order;

class SimpleOrder implements OrderInterface
{
    public function __construct(
        protected string $key,
        protected string $direction,
    ) {
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }
}
