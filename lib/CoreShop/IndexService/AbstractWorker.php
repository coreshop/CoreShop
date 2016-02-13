<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\IndexService;

use CoreShop\Model\Index;
use CoreShop\Model\Product;

abstract class AbstractWorker
{
    /**
     * @var Index
     */
    protected $index = null;

    /**
     * AbstractWorker constructor.
     * @param Index $index
     */
    public function __construct(Index $index)
    {
        $this->index = $index;
    }

    /**
     * creates or updates necessary index structures (like database tables and so on)
     *
     * @return void
     */
    abstract public function createOrUpdateIndexStructures();

    /**
     * deletes given element from index
     *
     * @param Product $object
     * @return void
     */
   abstract public function deleteFromIndex(Product $object);

    /**
     * updates given element in index
     *
     * @param Product $object
     * @return void
     */
    abstract public function updateIndex(Product $object);

    /**
     * returns product list implementation valid and configured for this worker/tenant
     *
     * @return Product\Listing
     */
    abstract public function getProductList();

    /**
     * @return Index
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param Index $index
     */
    public function setIndex($index)
    {
        $this->index = $index;
    }
}
