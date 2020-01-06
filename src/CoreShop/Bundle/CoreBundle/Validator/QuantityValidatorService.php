<?php

namespace CoreShop\Bundle\CoreBundle\Validator;

class QuantityValidatorService
{
    /**
     * @param mixed $minimumLimit
     * @param int   $quantity
     *
     * @return bool
     */
    public function isLowerThenMinLimit($minimumLimit, $quantity)
    {
        if (!is_numeric($minimumLimit)) {
            return false;
        }

        return $quantity < $minimumLimit;
    }

    /**
     * @param mixed $maximumLimit
     * @param int $quantity
     *
     * @return bool
     */
    public function isHigherThenMaxLimit($maximumLimit, $quantity)
    {
        if(!is_numeric($maximumLimit)) {
            return false;
        }

        return $quantity > $maximumLimit;
    }
}
