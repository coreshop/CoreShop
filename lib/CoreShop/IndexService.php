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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop;

use CoreShop\IndexService\AbstractWorker;
use CoreShop\IndexService\Mysql;
use CoreShop\Model\Index;
use CoreShop\Model\Product;
use Pimcore\Tool as PimTool;

/**
 * Class IndexService
 * @package CoreShop
 */
class IndexService
{
    /**
     * possible types of an index.
     *
     * @var array
     */
    public static $types = array('mysql');

    /**
     * IndexService.
     *
     * @var IndexService
     */
    protected static $indexService;

    /**
     * Workers.
     *
     * @var AbstractWorker[]
     */
    protected $worker;

    /**
     * Add new Index Tpye.
     *
     * @param $type
     */
    public static function addIndexType($type)
    {
        if (!in_array($type, self::$types)) {
            self::$types[] = $type;
        }
    }

    /**
     * get possible types.
     *
     * @return array
     */
    public static function getTypes()
    {
        return self::$types;
    }

    /**
     * Get Index Service Singleton.
     *
     * @return IndexService
     */
    public static function getIndexService()
    {
        if (is_null(self::$indexService)) {
            self::$indexService = new self();
        }

        return self::$indexService;
    }

    /**
     * IndexService constructor.
     */
    public function __construct()
    {
        $indexes = Index::getAll();
        $this->worker = array();

        foreach ($indexes as $index) {
            $class = '\\CoreShop\\IndexService\\'.ucfirst($index->getType());

            if (PimTool::classExists($class)) {
                $this->worker[] = new $class($index);
            }
        }
    }

    /**
     * Get Worker by Name.
     *
     * @param $name
     *
     * @return AbstractWorker|null
     */
    public function getWorker($name)
    {
        foreach ($this->worker as $worker) {
            if ($worker->getIndex()->getName() === $name) {
                return $worker;
            }
        }

        return null;
    }

    /**
     * Delete Product From Index.
     *
     * @param Product $product
     */
    public function deleteFromIndex(Product $product)
    {
        foreach ($this->worker as $worker) {
            $worker->deleteFromIndex($product);
        }
    }

    /**
     * Update Product in Index.
     *
     * @param Product $product
     */
    public function updateIndex(Product $product)
    {
        foreach ($this->worker as $worker) {
            $worker->updateIndex($product);
        }
    }
}
