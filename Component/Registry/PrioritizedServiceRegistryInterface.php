<?php

namespace CoreShop\Component\Registry;

interface PrioritizedServiceRegistryInterface
{
    /**
     * @return array
     */
    public function all();

    /**
     * @param string $identifier
     * @param int $priority
     * @param object $service
     *
     * @throws ExistingServiceException
     * @throws \InvalidArgumentException
     */
    public function register($identifier, $priority, $service);

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

    /**
     * get previous item to $identifier
     *
     * @param $identifier
     * @return mixed
     */
    public function getPreviousTo($identifier);

    /**
     * get all previous items to $identifier
     *
     * @param $identifier
     * @return array
     */
    public function getAllPreviousTo($identifier);

    /**
     * get previous item to $identifier
     *
     * @param $identifier
     * @return mixed
     */
    public function getNextTo($identifier);

    /**
     * get index for $identifier
     *
     * @param $identifier
     * @return int
     */
    public function getIndex($identifier);
}
