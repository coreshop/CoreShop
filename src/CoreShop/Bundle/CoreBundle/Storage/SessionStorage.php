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

namespace CoreShop\Bundle\CoreBundle\Storage;

use CoreShop\Component\Resource\Storage\StorageInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class SessionStorage implements StorageInterface
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    public function has(string $name): bool
    {
        return $this->requestStack->getSession()->has($name);
    }

    public function get(string $name, mixed $default = null): mixed
    {
        return $this->requestStack->getSession()->get($name, $default);
    }

    public function set(string $name, mixed $value): void
    {
        $this->requestStack->getSession()->set($name, $value);
    }

    public function remove(string $name): void
    {
        $this->requestStack->getSession()->remove($name);
    }

    public function all(): array
    {
        return $this->requestStack->getSession()->all();
    }
}
