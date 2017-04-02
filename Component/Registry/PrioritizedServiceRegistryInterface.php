<?php

namespace CoreShop\Component\Registry;

use Zend\Stdlib\PriorityQueue;

interface PrioritizedServiceRegistryInterface
{
    /**
     * @return PriorityQueue
     */
    public function all();

    /**
     * @param object $service
     * @param int $priority
     *
     * @throws ExistingServiceException
     * @throws \InvalidArgumentException
     */
    public function register($service, $priority = 0);

    /**
     * @param object $service
     *
     * @throws NonExistingServiceException
     */
    public function unregister($service);

    /**
     * @param object $service
     *
     * @return bool
     */
    public function has($service);
}
