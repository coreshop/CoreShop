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

namespace CoreShop\Component\Registry;

interface PrioritizedServiceRegistryInterface
{
    /**
     * @return array
     */
    public function all();

    /**
     * @param string $identifier
     * @param int    $priority
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
     * get previous item to $identifier.
     *
     * @param string $identifier
     *
     * @return mixed
     */
    public function getPreviousTo($identifier);

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function hasPreviousTo($identifier);

    /**
     * get all previous items to $identifier.
     *
     * @param string $identifier
     *
     * @return array
     */
    public function getAllPreviousTo($identifier);

    /**
     * get previous item to $identifier.
     *
     * @param string $identifier
     *
     * @return mixed
     */
    public function getNextTo($identifier);

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function hasNextTo($identifier);

    /**
     * get index for $identifier.
     *
     * @param string $identifier
     *
     * @return int
     */
    public function getIndex($identifier);
}
