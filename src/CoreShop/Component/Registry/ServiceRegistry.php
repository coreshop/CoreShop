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

namespace CoreShop\Component\Registry;

class ServiceRegistry implements ServiceRegistryInterface
{
    private array $services = [];

    public function __construct(private string $interface, private string $context = 'service')
    {
    }

    public function all(): array
    {
        return $this->services;
    }

    public function register(string $identifier, object $service): void
    {
        if ($this->has($identifier)) {
            throw new ExistingServiceException($this->context, $identifier);
        }

        if (!in_array($this->interface, class_implements($service), true)) {
            throw new \InvalidArgumentException(sprintf('%s needs to implement "%s", "%s" given.', ucfirst($this->context), $this->interface, $service::class));
        }

        $this->services[$identifier] = $service;
    }

    public function unregister(string $identifier): void
    {
        if (!$this->has($identifier)) {
            throw new NonExistingServiceException($this->context, $identifier, array_keys($this->services));
        }

        unset($this->services[$identifier]);
    }

    public function has(string $identifier): bool
    {
        return isset($this->services[$identifier]);
    }

    public function get(string $identifier): object
    {
        if (!$this->has($identifier)) {
            throw new NonExistingServiceException($this->context, $identifier, array_keys($this->services));
        }

        return $this->services[$identifier];
    }
}
