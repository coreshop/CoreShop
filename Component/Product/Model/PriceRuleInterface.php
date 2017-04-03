<?php

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Rule\Model\RuleInterface;

interface PriceRuleInterface extends RuleInterface
{
    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     * @return static
     */
    public function setDescription($description);

    /**
     * @return boolean
     */
    public function getActive();

    /**
     * @param boolean $active
     * @return static
     */
    public function setActive($active);
}