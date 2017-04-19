<?php

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Rule\Model\RuleInterface;

interface ProductSpecificPriceRuleInterface extends RuleInterface
{
    /**
     * @return bool
     */
    public function getInherit();

    /**
     * @param bool $inherit
     *
     * @return static
     */
    public function setInherit($inherit);

     /**
     * @return bool
     */
    public function getPriority();

    /**
     * @param int $priority
     *
     * @return static
     */
    public function setPriority($priority);

    /**
     * @return int
     */
    public function getProduct();

    /**
     * @param integer $id
     */
    public function setProduct($id);
}
