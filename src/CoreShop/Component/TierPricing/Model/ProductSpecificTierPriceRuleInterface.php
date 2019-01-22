<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\TierPricing\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;
use CoreShop\Component\Resource\Model\ToggleableInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;

use Doctrine\Common\Collections\Collection;

interface ProductSpecificTierPriceRuleInterface extends ResourceInterface, TimestampableInterface, ToggleableInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return Collection|ConditionInterface[]
     */
    public function getConditions();

    /**
     * @return bool
     */
    public function hasConditions();

    /**
     * @param ConditionInterface $conditions
     *
     * @return bool
     */
    public function hasCondition(ConditionInterface $conditions);

    /**
     * @param ConditionInterface $conditions
     */
    public function addCondition(ConditionInterface $conditions);

    /**
     * @param ConditionInterface $conditions
     */
    public function removeCondition(ConditionInterface $conditions);

    /**
     * @return Collection|ProductTierPriceRangeInterface[]
     */
    public function getRanges();

    /**
     * @return bool
     */
    public function hasRanges();

    /**
     * @param ProductTierPriceRangeInterface $priceRange
     *
     * @return bool
     */
    public function hasRange(ProductTierPriceRangeInterface $priceRange);

    /**
     * @param ProductTierPriceRangeInterface $priceRange
     */
    public function addRange(ProductTierPriceRangeInterface $priceRange);

    /**
     * @param ProductTierPriceRangeInterface $priceRange
     */
    public function removeRange(ProductTierPriceRangeInterface $priceRange);

    /**
     * @return bool
     */
    public function getInherit();

    /**
     * @param bool $inherit
     */
    public function setInherit($inherit);

    /**
     * @return int
     */
    public function getPriority();

    /**
     * @param int $priority
     */
    public function setPriority($priority);

    /**
     * @return int
     */
    public function getProduct();

    /**
     * @param int $id
     */
    public function setProduct($id);
}
