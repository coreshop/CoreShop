<?php
declare(strict_types=1);

namespace CoreShop\Component\Resource\Exception;

use Pimcore\Model\Element\ValidationException;

class NiceValidationException extends ValidationException
{
    public function __toString()
    {
        return $this->getMessage();
    }
}
