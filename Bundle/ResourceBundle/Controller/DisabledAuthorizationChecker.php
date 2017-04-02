<?php

namespace CoreShop\Bundle\ResourceBundle\Controller;

final class DisabledAuthorizationChecker implements AuthorizationCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isGranted(RequestConfiguration $requestConfiguration, $permission)
    {
        return true;
    }
}
