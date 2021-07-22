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

namespace CoreShop\Bundle\IndexBundle\Worker;

use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use Pimcore\Tool;

abstract class AbstractListing implements ListingInterface
{
    /**
     * @var IndexInterface
     */
    protected $index;

    /**
     * @var WorkerInterface
     */
    protected $worker;

    /**
     * @var string
     */
    protected $locale;

    /**
     * {@inheritdoc}
     */
    public function __construct(IndexInterface $index, WorkerInterface $worker)
    {
        $this->index = $index;
        $this->worker = $worker;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function getObjects();

    /**
     * {@inheritdoc}
     */
    abstract public function addCondition(ConditionInterface $condition, $fieldName);

    /**
     * {@inheritdoc}
     */
    abstract public function addQueryCondition(ConditionInterface $condition, $fieldName);

    /**
     * {@inheritdoc}
     */
    abstract public function addRelationCondition(ConditionInterface $condition, $fieldName);

    /**
     * {@inheritdoc}
     */
    abstract public function resetCondition($fieldName);

    /**
     * {@inheritdoc}
     */
    abstract public function resetQueryCondition($fieldName);

    /**
     * {@inheritdoc}
     */
    abstract public function resetConditions();

    /**
     * {@inheritdoc}
     */
    abstract public function setOrder($order);

    /**
     * {@inheritdoc}
     */
    abstract public function getOrder();

    /**
     * {@inheritdoc}
     */
    abstract public function setOrderKey($orderKey);

    /**
     * {@inheritdoc}
     */
    abstract public function getOrderKey();

    /**
     * {@inheritdoc}
     */
    abstract public function setLimit($limit);

    /**
     * {@inheritdoc}
     */
    abstract public function getLimit();

    /**
     * {@inheritdoc}
     */
    abstract public function setOffset($offset);

    /**
     * {@inheritdoc}
     */
    abstract public function getOffset();

    /**
     * {@inheritdoc}
     */
    abstract public function setCategory(PimcoreModelInterface $category);

    /**
     * {@inheritdoc}
     */
    abstract public function getCategory();

    /**
     * {@inheritdoc}
     */
    abstract public function setVariantMode($variantMode);

    /**
     * {@inheritdoc}
     */
    abstract public function getVariantMode();

    /**
     * {@inheritdoc}
     */
    abstract public function load(array $options = []);

    /**
     * {@inheritdoc}
     */
    abstract public function getGroupByValues($fieldName, $countValues = false, $fieldNameShouldBeExcluded = true);

    /**
     * {@inheritdoc}
     */
    abstract public function getGroupByRelationValues($fieldName, $countValues = false, $fieldNameShouldBeExcluded = true);

    /**
     * {@inheritdoc}
     */
    abstract public function getGroupBySystemValues($fieldName, $countValues = false, $fieldNameShouldBeExcluded = true);

    /**
     * {@inheritdoc}
     */
    abstract public function buildSimilarityOrderBy($fields, $objectId);

    /**
     * {@inheritdoc}
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function setIndex(IndexInterface $index)
    {
        $this->index = $index;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        //TODO: Use Locale Services
        if (is_null($this->locale)) {
            $language = null;

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
