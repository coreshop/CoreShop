<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Configuration\Model;

use CoreShop\Component\Core\Model\ResourceInterface;

interface ConfigurationInterface extends ResourceInterface
{
    /**
     * @return string
     * @return static
     */
    public function getKey();

    /**
     * @param string $key
     * @return static
     */
    public function setKey($key);

    /**
     * @return string
     * @return static
     */
    public function getData();

    /**
     * @param string $data
     * @return static
     */
    public function setData($data);

    /**
     * @return int
     */
    public function getCreationDate();

    /**
     * @param int $creationDate
     * @return static
     */
    public function setCreationDate($creationDate);

    /**
     * @return int
     */
    public function getModificationDate();

    /**
     * @param int $modificationDate
     * @return static
     */
    public function setModificationDate($modificationDate);

    /**
     * @return int
     */
    public function getShopId();

    /**
     * @param int $shopId
     * @return static
     */
    public function setShopId($shopId);
}