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

use CoreShop\IndexService\Condition;
use CoreShop\Model\Category;
use CoreShop\Model\Index;
use CoreShop\Model\Product;
use CoreShop\Model\Shop;
use CoreShop\Exception;
use Pimcore\Tool;

/**
 * Class Listing
 * @package CoreShop\Model\Product
 */
abstract class Listing implements \Zend_Paginator_Adapter_Interface, \Zend_Paginator_AdapterAggregate, \Iterator
{
    const ORDERKEY_PRICE = 'orderkey_price';

    /**
     * Variant mode defines how to consider variants in product list results
     * - does not consider variants in search results.
     */
    const VARIANT_MODE_HIDE = 'hide';

    /**
     * Variant mode defines how to consider variants in product list results
     * - considers variants in search results and returns objects and variants.
     */
    const VARIANT_MODE_INCLUDE = 'include';

    /**
     * Variant mode defines how to consider variants in product list results
     * - considers variants in search results but only returns corresponding objects in search results.
     */
    const VARIANT_MODE_INCLUDE_PARENT_OBJECT = 'include_parent_object';

    /**
     * @var Index|null
     */
    public $index = null;

    /**
     * @var string
     */
    protected $locale;

    /**
     * Listing constructor.
     *
     * @param Index $index
     */
    public function __construct(Index $index)
    {
        $this->index = $index;
    }

    /**
     * Returns all products valid for this search.
     *
     * @return \CoreShop\Model\Product[]
     */
    abstract public function getProducts();

    /**
     * Adds filter condition to product list
     * Fieldname is optional but highly recommended - needed for resetting condition based on fieldname
     * and exclude functionality in group by results.
     *
     * @param Condition $condition
     * @param string $fieldName
     */
    abstract public function addCondition(Condition $condition, $fieldName);

    /**
     * Adds query condition to product list for fulltext search
     * Fieldname is optional but highly recommended - needed for resetting condition based on fieldname
     * and exclude functionality in group by results.
     *
     * @param Condition $condition
     * @param string $fieldName
     */
    abstract public function addQueryCondition(Condition $condition, $fieldName);

    /**
     * Adds relation condition to product list.
     *
     * @param Condition $condition
     * @param string $fieldName
     */
    abstract public function addRelationCondition(Condition $condition, $fieldName);

    /**
     * Reset filter condition for fieldname.
     *
     * @param $fieldName
     */
    abstract public function resetCondition($fieldName);

    /**
     * Reset query condition for fieldname.
     *
     * @param $fieldName
     */
    abstract public function resetQueryCondition($fieldName);

    /**
     * Resets all conditions of product list.
     */
    abstract public function resetConditions();

    /**
     * sets order direction.
     *
     * @param $order
     */
    abstract public function setOrder($order);

    /**
     * gets order direction.
     *
     * @return string
     */
    abstract public function getOrder();

    /**
     * sets order key.
     *
     * @param $orderKey string | array  - either single field name, or array of field names or array of arrays (field name, direction)
     */
    abstract public function setOrderKey($orderKey);

    /**
     * @return string | array
     */
    abstract public function getOrderKey();

    /**
     * @param $limit int
     */
    abstract public function setLimit($limit);

    /**
     * @return int
     */
    abstract public function getLimit();

    /**
     * @param $offset int
     */
    abstract public function setOffset($offset);

    /**
     * @return int
     */
    abstract public function getOffset();

    /**
     * @param $category
     */
    abstract public function setCategory(Category $category);

    /**
     * @return \CoreShop\Model\Category
     */
    abstract public function getCategory();

    /**
     * @param $shop
     */
    abstract public function setShop(Shop $shop);

    /**
     * @return \CoreShop\Model\Shop
     */
    abstract public function getShop();

    /**
     * @param $variantMode
     */
    abstract public function setVariantMode($variantMode);

    /**
     * @return string
     */
    abstract public function getVariantMode();

    /**
     * loads search results from index and returns them.
     *
     * @return Product[]
     */
    abstract public function load();

    /**
     * loads group by values based on fieldname either from local variable if prepared or directly from product index.
     *
     * @param $fieldName
     * @param bool $countValues
     * @param bool $fieldNameShouldBeExcluded => set to false for and-conditions
     *
     * @return array
     *
     * @throws Exception
     */
    abstract public function getGroupByValues($fieldName, $countValues = false, $fieldNameShouldBeExcluded = true);

    /**
     * loads group by values based on relation fieldname either from local variable if prepared or directly from product index.
     *
     * @param      $fieldName
     * @param bool $countValues
     * @param bool $fieldNameShouldBeExcluded => set to false for and-conditions
     *
     * @return array
     *
     * @throws Exception
     */
    abstract public function getGroupByRelationValues($fieldName, $countValues = false, $fieldNameShouldBeExcluded = true);

    /**
     * loads group by values based on relation fieldname either from local variable if prepared or directly from product index.
     *
     * @param      $fieldName
     * @param bool $countValues
     * @param bool $fieldNameShouldBeExcluded => set to false for and-conditions
     *
     * @return array
     *
     * @throws Exception
     */
    abstract public function getGroupBySystemValues($fieldName, $countValues = false, $fieldNameShouldBeExcluded = true);

    /**
     * returns order by statement for similarity calculations based on given fields and object ids
     * returns cosine similarity calculation
     *
     * @param $fields
     * @param $objectId
     *
     * @return Product[]
     */
    abstract public function buildSimilarityOrderBy($fields, $objectId);

    /**
     * @return Index|null
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param Index|null $index
     */
    public function setIndex($index)
    {
        $this->index = $index;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        if (is_null($this->locale)) {
            $language = null;

            if (\Zend_Registry::isRegistered("Zend_Locale")) {
                $language = \Zend_Registry::get("Zend_Locale");
                if (Tool::isValidLanguage((string) $language)) {
                    $language = (string) $language;
                }
            }

            if (!$language) {
                $language = Tool::getDefaultLanguage();
            }

            $this->locale = $language;
        }

        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }
}
