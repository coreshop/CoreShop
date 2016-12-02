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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Product;

use CoreShop\Model\AbstractModel;
use CoreShop\Model\Index;
use CoreShop\Model\Product\Filter\Condition\AbstractCondition;
use CoreShop\Model\Product\Filter\Similarity\AbstractSimilarity;
use Pimcore\Tool;

/**
 * Class Filter
 * @package CoreShop\Model\Product
 */
class Filter extends AbstractModel
{
    /**
     * possible types of a condition.
     *
     * @var array
     */
    public static $availableConditions = array('select', 'multiselect', 'range', 'boolean', 'combined');

    /**
     * possible types of a condition.
     *
     * @var array
     */
    public static $availableSimilarities = array('field');


    /**
     * Add Condition Type.
     *
     * @param $condition
     */
    public static function addCondition($condition)
    {
        if (!in_array($condition, self::$availableConditions)) {
            self::$availableConditions[] = $condition;
        }
    }

    /**
     * Get Condition Types.
     *
     * @return array
     */
    public static function getConditions()
    {
        return self::$availableConditions;
    }

    /**
     * Add Similarity Type.
     *
     * @param $similarity
     */
    public static function addSimilarityType($similarity)
    {
        if (!in_array($similarity, self::$availableSimilarities)) {
            self::$availableSimilarities[] = $similarity;
        }
    }

    /**
     * Get Similarity Types.
     *
     * @return array
     */
    public static function getSimilarityTypes()
    {
        return self::$availableSimilarities;
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
     * @param $conditionNamespace
     * @return mixed
     * @throws \CoreShop\Exception
     */
    public function prepareConditions($conditions, $conditionNamespace) {
        $conditionInstances = array();

        foreach ($conditions as $condition) {
            $class = $conditionNamespace.ucfirst($condition['type']);

            if (Tool::classExists($class))
            {
                if($condition['type'] === "combined")
                {
                    $nestedConditions = static::prepareConditions($condition['conditions'], $conditionNamespace);
                    $condition['conditions'] = $nestedConditions;
                }

                $instance = new $class();
                $instance->setValues($condition);

                $conditionInstances[] = $instance;
            } else {
                throw new \CoreShop\Exception(sprintf('Condition with type %s not found'), $condition['type']);
            }
        }

        return $conditionInstances;
    }

    /**
     * @param $similarities
     * @param $similarityNamespace
     * @return mixed
     * @throws \CoreShop\Exception
     */
    public function prepareSimilarities($similarities, $similarityNamespace) {
        $instances = array();

        foreach ($similarities as $sim) {
            $class = $similarityNamespace.ucfirst($sim['type']);

            if (Tool::classExists($class))
            {
                $instance = new $class();
                $instance->setValues($sim);

                $instances[] = $instance;
            } else {
                throw new \CoreShop\Exception(sprintf('Similarity with type %s not found'), $sim['type']);
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
