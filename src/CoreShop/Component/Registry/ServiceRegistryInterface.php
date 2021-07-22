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
