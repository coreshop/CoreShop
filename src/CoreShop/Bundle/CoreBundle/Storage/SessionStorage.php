<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Storage;

use CoreShop\Component\Resource\Storage\StorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class SessionStorage implements StorageInterface
{
    public function __construct(private SessionInterface $session)
    {
    }

    public function has(string $name): bool
    {
        return $this->session->has($name);
    }

    public function get(string $name, mixed $default = null): mixed
    {
        return $this->session->get($name, $default);
    }

    public function set(string $name, mixed $value): void
    {
        $this->session->set($name, $value);
    }

    public function remove(string $name): void
    {
        $this->session->remove($name);
    }

    public function all(): array
    {
        return $this->session->all();
    }
}
