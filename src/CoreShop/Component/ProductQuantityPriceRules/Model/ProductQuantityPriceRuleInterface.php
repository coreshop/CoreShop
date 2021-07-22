<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\ProductQuantityPriceRules\Model;

use CoreShop\Component\Rule\Model\ConditionInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use Doctrine\Common\Collections\Collection;

interface ProductQuantityPriceRuleInterface extends RuleInterface
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
     * @return string
     */
    public function getCalculationBehaviour();

    /**
     * @param string $calculationBehaviour
     */
    public function setCalculationBehaviour($calculationBehaviour);

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
     * @return Collection|QuantityRangeInterface[]
     */
    public function getRanges();

    /**
     * @return bool
     */
    public function hasRanges();

    /**
     * @param QuantityRangeInterface $priceRange
     *
     * @return bool
     */
    public function hasRange(QuantityRangeInterface $priceRange);

    /**
     * @param QuantityRangeInterface $priceRange
     */
    public function addRange(QuantityRangeInterface $priceRange);

    /**
     * @param QuantityRangeInterface $priceRange
     */
    public function removeRange(QuantityRangeInterface $priceRange);

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
