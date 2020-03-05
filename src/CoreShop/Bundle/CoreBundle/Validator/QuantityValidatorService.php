<?php

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Validator;

class QuantityValidatorService
{
    /**
     * @param mixed $minimumLimit
     * @param float $quantity
     *
     * @return bool
     */
    public function isLowerThenMinLimit($minimumLimit, float $quantity): bool
    {
        if (!is_numeric($minimumLimit)) {
            return false;
        }

        return $quantity < $minimumLimit;
    }

    /**
     * @param mixed $maximumLimit
     * @param float $quantity
     *
     * @return bool
     */
    public function isHigherThenMaxLimit($maximumLimit, float $quantity): bool
    {
        if(!is_numeric($maximumLimit)) {
            return false;
        }

        return $quantity > $maximumLimit;
    }
}
