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

namespace CoreShop\Behat\Service;

interface SharedStorageInterface
{
    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key);

    /**
     * @param string $key
     * @param mixed  $resource
     */
    public function set($key, $resource);

    /**
     * @return mixed
     */
    public function getLatestResource();

    /**
     * @param array $clipboard
     *
     * @throws \RuntimeException
     */
    public function setClipboard(array $clipboard);
}
