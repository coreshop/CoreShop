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

namespace CoreShop\Component\Core\Repository;

use CoreShop\Component\Resource\Model\ResourceInterface;

/**
 * Interface RepositoryInterface
 * @package CoreShop\Component\Core\Repository
 */
interface RepositoryInterface {
    const ORDER_ASCENDING = 'ASC';
    const ORDER_DESCENDING = 'DESC';

    /**
     * @param ResourceInterface $resource
     */
    public function add(ResourceInterface $resource);

    /**
     * @param ResourceInterface $resource
     */
    public function remove(ResourceInterface $resource);


    /**
     * @return array
     */
    public function getAll();
}