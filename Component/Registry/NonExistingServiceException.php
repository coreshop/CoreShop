<?php

namespace CoreShop\Component\Registry;

class NonExistingServiceException extends \InvalidArgumentException
{
    public function __construct($context, $type, array $existingServices)
    {
        parent::__construct(sprintf(
            '%s service "%s" does not exist, available %s services: "%s"',
            ucfirst($context),
            $type,
            $context,
            implode('", "', $existingServices)
        ));
    }
}
