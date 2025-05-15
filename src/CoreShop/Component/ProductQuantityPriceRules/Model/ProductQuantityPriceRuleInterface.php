<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\ProductQuantityPriceRules\Model;

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
     * @return Collection|QuantityRangeInterface[]
     */
    public function getRanges();

    /**
     * @return bool
     */
    public function hasRanges();

    /**
     * @return bool
     */
    public function hasRange(QuantityRangeInterface $priceRange);

    public function addRange(QuantityRangeInterface $priceRange);

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
     * @param int $product
     */
    public function setProduct($product);
}
