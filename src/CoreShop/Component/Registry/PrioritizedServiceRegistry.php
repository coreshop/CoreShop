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

namespace CoreShop\Component\Registry;

final class PrioritizedServiceRegistry implements PrioritizedServiceRegistryInterface
{
    /**
     * @var PriorityMap
     */
    private $priortyMap;

    /**
     * Interface which is required by all services.
     *
     * @var string
     */
    private $interface;

    /**
     * Human readable context for these services, e.g. "grid field".
     *
     * @var string
     */
    private $context;

    /**
     * @param string $interface
     * @param string $context
     */
    public function __construct($interface, $context = 'service')
    {
        $this->interface = $interface;
        $this->context = $context;
        $this->priortyMap = new PriorityMap();
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->priortyMap->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function register($identifier, $priority, $service)
    {
        if ($this->has($identifier)) {
            throw new ExistingServiceException($this->context, $identifier);
        }

        if (!is_object($service)) {
            throw new \InvalidArgumentException(sprintf('%s needs to be an object, %s given.', ucfirst($this->context), gettype($service)));
        }

        if (!in_array($this->interface, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', ucfirst($this->context), $this->interface, get_class($service))
            );
        }

        $this->priortyMap->set($identifier, $service, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function unregister($identifier)
    {
        if (!$this->has($identifier)) {
            throw new NonExistingServiceException($this->context, $identifier, $this->priortyMap->getKeys());
        }

        $this->priortyMap->remove($identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function has($identifier)
    {
        return $this->priortyMap->has($identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function get($identifier)
    {
        if (!$this->has($identifier)) {
            throw new NonExistingServiceException($this->context, $identifier, $this->priortyMap->getKeys());
        }

        return $this->priortyMap->get($identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function getNextTo($identifier)
    {
        $keys = $this->priortyMap->getKeys();
        $nextIndex = -1;

        foreach ($keys as $index => $key) {
            if ($key === $identifier) {
                $nextIndex = $index + 1;

                break;
            }
        }

        if (count($keys) > $nextIndex) {
            return $this->get($keys[$nextIndex]);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasNextTo($identifier)
    {
        $keys = $this->priortyMap->getKeys();
        $nextIndex = -1;

        foreach ($keys as $index => $key) {
            if ($key === $identifier) {
                $nextIndex = $index + 1;

                break;
            }
        }

        return $this->has($keys[$nextIndex]);
    }

    /**
     * @param string $identifier
     *
     * @return bool|int|string
     */
    private function getPreviousIndex($identifier)
    {
        $keys = $this->priortyMap->getKeys();
        $prevIndex = -1;

        foreach ($keys as $index => $key) {
            if ($key == $identifier) {
                $prevIndex = $index - 1;

                break;
            }
        }

        return $prevIndex >= 0 ? $prevIndex : -1;
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviousTo($identifier)
    {
        $keys = $this->priortyMap->getKeys();
        $prevIndex = $this->getPreviousIndex($identifier);

        if ($prevIndex >= 0) {
            return $this->get($keys[$prevIndex]);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPreviousTo($identifier)
    {
        $prevIndex = $this->getPreviousIndex($identifier);

        return $prevIndex >= 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllPreviousTo($identifier)
    {
        $keys = $this->priortyMap->getKeys();
        $prevIndex = $this->getPreviousIndex($identifier);

        if ($prevIndex >= 0) {
            $previousElements = [];

            for ($i = $prevIndex; $i > 0; $i--) {
                $previousElements[] = $this->get($keys[$i]);
            }

            return $previousElements;
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getIndex($identifier)
    {
        $keys = $this->priortyMap->getKeys();

        return array_search($identifier, $keys);
    }
}
