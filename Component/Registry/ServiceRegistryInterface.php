<?php

namespace CoreShop\Component\Registry;

interface ServiceRegistryInterface
{
    /**
     * @return array
     */
    public function all();

    /**
     * @param string $identifier
     * @param object $service
     *
     * @throws ExistingServiceException
     * @throws \InvalidArgumentException
     */
    public function register($identifier, $service);

    /**
     * @param string $identifier
     *
     * @throws NonExistingServiceException
     */
    public function unregister($identifier);

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function has($identifier);

    /**
     * @param string $identifier
     *
     * @return object
     *
     * @throws NonExistingServiceException
     */
    public function get($identifier);
}
