<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Storage;

use CoreShop\Component\Resource\Storage\StorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class SessionStorage implements StorageInterface
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $name): bool
    {
        return $this->session->has($name);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name, $default = null)
    {
        return $this->session->get($name, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $name, $value): void
    {
        $this->session->set($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $name): void
    {
        $this->session->remove($name);
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return $this->session->all();
    }
}
