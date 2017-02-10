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
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop;

use CoreShop\Composite\Dispatcher;
use CoreShop\IndexService\AbstractWorker;
use CoreShop\IndexService\Elasticsearch;
use CoreShop\IndexService\Getter\AbstractGetter;
use CoreShop\IndexService\Getter\Brick;
use CoreShop\IndexService\Getter\Classificationstore;
use CoreShop\IndexService\Getter\Fieldcollection;
use CoreShop\IndexService\Getter\Localizedfield;
use CoreShop\IndexService\Interpreter\AbstractInterpreter;
use CoreShop\IndexService\Interpreter\LocalizedInterpreter;
use CoreShop\IndexService\Interpreter\Object;
use CoreShop\IndexService\Interpreter\ObjectId;
use CoreShop\IndexService\Interpreter\ObjectIdSum;
use CoreShop\IndexService\Interpreter\ObjectProperty;
use CoreShop\IndexService\Interpreter\Soundex;
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
     * @var Dispatcher
     */
    public static $indexDispatcher;

    /**
     * @var Dispatcher
     */
    public static $getterDispatcher;

    /**
     * @var Dispatcher
     */
    public static $interpreterDispatcher;

    /**
     * @return Dispatcher
     */
    public static function getIndexDispatcher()
    {
        if (is_null(self::$indexDispatcher)) {
            self::$indexDispatcher = new Dispatcher('indexService.provider', AbstractWorker::class);

            self::$indexDispatcher->addTypes([
                Mysql::class,
                Elasticsearch::class
            ]);
        }

        return self::$indexDispatcher;
    }

    /**
     * @return Dispatcher
     */
    public static function getGetterDispatcher()
    {
        if (is_null(self::$getterDispatcher)) {
            self::$getterDispatcher = new Dispatcher('indexService.getter', AbstractGetter::class);

            self::$getterDispatcher->addTypes([
                Brick::class,
                Classificationstore::class,
                Fieldcollection::class,
                Localizedfield::class
            ]);
        }

        return self::$getterDispatcher;
    }

    /**
     * @return Dispatcher
     */
    public static function getInterpreterDispatcher()
    {
        if (is_null(self::$interpreterDispatcher)) {
            self::$interpreterDispatcher = new Dispatcher('indexService.interpreter', AbstractInterpreter::class);

            self::$interpreterDispatcher->addTypes([
                LocalizedInterpreter::class,
                Object::class,
                ObjectId::class,
                ObjectIdSum::class,
                ObjectProperty::class,
                Soundex::class
            ]);
        }

        return self::$interpreterDispatcher;
    }

    /**
     * Add new Index Type.
     *
     * @param $type
     *
     * @deprecated will be removed with version 1.3
     */
    public static function addIndexType($type)
    {
        self::getIndexDispatcher()->addType('CoreShop\IndexService\\' . ucfirst($type));
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
        $this->worker = [];

        foreach ($indexes as $index) {
            $className = static::getIndexDispatcher()->getClassForType($index->getType());

            if (PimTool::classExists($className)) {
                $this->worker[] = new $className($index);
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
