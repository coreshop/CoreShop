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

namespace CoreShop\Model\Product;

use CoreShop\Model\AbstractModel;
use CoreShop\Model\Index;
use CoreShop\Model\Product\Filter\Condition\AbstractCondition;
use CoreShop\Model\Product\Filter\Condition\Boolean;
use CoreShop\Model\Product\Filter\Condition\Combined;
use CoreShop\Model\Product\Filter\Condition\Multiselect;
use CoreShop\Model\Product\Filter\Condition\Range;
use CoreShop\Model\Product\Filter\Condition\Select;
use CoreShop\Composite\Dispatcher;
use CoreShop\Model\Product\Filter\Similarity\AbstractSimilarity;
use CoreShop\Model\Product\Filter\Similarity\Field;
use Pimcore\Tool;

/**
 * Class Filter
 * @package CoreShop\Model\Product
 */
class Filter extends AbstractModel
{
    /**
     * @var Dispatcher
     */
    public static $conditionsDispatcher;

    /**
     * @var Dispatcher
     */
    public static $similarityDispatcher;

    /**
     * @return Dispatcher
     */
    public static function getConditionDispatcher()
    {
        if(is_null(self::$conditionsDispatcher)) {
            self::$conditionsDispatcher = new Dispatcher('filter.condition', AbstractCondition::class);

            self::$conditionsDispatcher->addTypes([
                Select::class,
                Multiselect::class,
                Range::class,
                Boolean::class,
                Combined::class
            ]);
        }

        return self::$conditionsDispatcher;
    }

    /**
     * @return Dispatcher
     */
    public static function getSimilaritiesDispatcher()
    {
        if(is_null(self::$similarityDispatcher)) {
            self::$similarityDispatcher = new Dispatcher('filter.similarity', AbstractSimilarity::class);

            self::$similarityDispatcher->addTypes([
                Field::class
            ]);
        }

        return self::$similarityDispatcher;
    }

    /**
     * Add Condition Type.
     *
     * @deprecated will be removed with 1.3
     * @param $condition
     */
    public static function addCondition($condition)
    {
        self::getConditionDispatcher()->addType('CoreShop\Model\Product\Filter\Condition\\' . ucfirst($condition));
    }

    /**
     * Add Similarity Type.
     *
     * @deprecated will be removed with 1.3
     * @param $similarity
     */
    public static function addSimilarityType($similarity)
    {
        self::getSimilaritiesDispatcher()->addType('CoreShop\Model\Product\Filter\Similarity\\' . ucfirst($similarity));
    }

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $resultsPerPage;

    /**
     * @var string
     */
    public $order;

    /**
     * @var string
     */
    public $orderKey;

    /**
     * @var AbstractCondition[]
     */
    public $preConditions;

    /**
     * @var AbstractCondition[]
     */
    public $filters;

    /**
     * @var AbstractSimilarity[]
     */
    public $similarities;

    /**
     * @var int
     */
    public $index;

    /**
     * @var Index
     */
    public $indexObject;

    /**
     * @var boolean
     */
    public $useShopPagingSettings;

    /**
     * @param $conditions
     *
     * @return mixed
     * @throws \CoreShop\Exception
     */
    public function prepareConditions($conditions)
    {
        $conditionInstances = [];

        foreach ($conditions as $condition) {
            $className = static::getConditionDispatcher()->getClassForType($condition['type']);

            if ($className && Tool::classExists($className)) {
                if ($condition['type'] === "combined") {
                    $nestedConditions = static::prepareConditions($condition['conditions']);
                    $condition['conditions'] = $nestedConditions;
                }

                $instance = new $className();
                $instance->setValues($condition);

                $conditionInstances[] = $instance;
            } else {
                throw new \CoreShop\Exception(sprintf('Condition with type %s and class %s not found', $condition['type'], $className));
            }
        }

        return $conditionInstances;
    }

    /**
     * @param $similarities
     *
     * @return mixed
     * @throws \CoreShop\Exception
     */
    public function prepareSimilarities($similarities)
    {
        $instances = [];

        foreach ($similarities as $sim) {
            $className = static::getSimilaritiesDispatcher()->getClassForType($sim['type']);

            if ($className && Tool::classExists($className)) {
                $instance = new $className();
                $instance->setValues($sim);

                $instances[] = $instance;
            } else {
                throw new \CoreShop\Exception(sprintf('Similarity with type %s and class %s not found', $sim['type'], $className));
            }
        }

        return $instances;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getResultsPerPage()
    {
        return $this->resultsPerPage;
    }

    /**
     * @param int $resultsPerPage
     */
    public function setResultsPerPage($resultsPerPage)
    {
        $this->resultsPerPage = $resultsPerPage;
    }

    /**
     * @return string
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param string $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return mixed
     */
    public function getOrderKey()
    {
        return $this->orderKey;
    }

    /**
     * @param mixed $orderKey
     */
    public function setOrderKey($orderKey)
    {
        $this->orderKey = $orderKey;
    }

    /**
     * @return AbstractCondition[]
     */
    public function getPreConditions()
    {
        return $this->preConditions;
    }

    /**
     * @param AbstractCondition[] $preConditions
     */
    public function setPreConditions($preConditions)
    {
        $this->preConditions = $preConditions;
    }

    /**
     * @return AbstractCondition[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param AbstractCondition[] $filters
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return Filter\Similarity\AbstractSimilarity[]
     */
    public function getSimilarities()
    {
        return $this->similarities;
    }

    /**
     * @param Filter\Similarity\AbstractSimilarity[] $similarities
     */
    public function setSimilarities($similarities)
    {
        $this->similarities = $similarities;
    }

    /**
     * @return Index
     */
    public function getIndex()
    {
        if (!$this->indexObject instanceof Index) {
            $this->indexObject = Index::getById($this->index);
        }

        return $this->indexObject;
    }

    /**
     * @param Index $index
     */
    public function setIndex($index)
    {
        if (!$index instanceof Index) {
            $this->indexObject = Index::getById($index);
        }

        $this->index = $index;
    }

    /**
     * @return boolean
     */
    public function getUseShopPagingSettings()
    {
        return $this->useShopPagingSettings;
    }

    /**
     * @param boolean $useShopPagingSettings
     */
    public function setUseShopPagingSettings($useShopPagingSettings)
    {
        $this->useShopPagingSettings = $useShopPagingSettings;
    }
}
