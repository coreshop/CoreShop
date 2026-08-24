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

namespace CoreShop\Bundle\CoreBundle\Security;

use CoreShop\Bundle\ResourceBundle\Controller\RedirectUrlValidationTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;
use Symfony\Component\Security\Http\ParameterBagUtils;

/**
 * Applies the same allow-list as the frontend controllers to the target URL of a failed login.
 *
 * Symfony accepts any "_failure_path" that starts with "/" or "http". As it is evaluated on a
 * failed login attempt, an attacker only needs the victim to submit the login form once, without
 * ever learning the credentials, to have the browser end up on a foreign host.
 */
class ShopUserAuthenticationFailureHandler extends DefaultAuthenticationFailureHandler
{
    use RedirectUrlValidationTrait;

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $parameter = $this->options['failure_path_parameter'];
        $failureUrl = ParameterBagUtils::getRequestParameterValue($request, $parameter);

        if (is_string($failureUrl) && '' === $this->validateRedirectUrl($request, $failureUrl, '')) {
            // Drop the rejected value so the parent falls back to the configured failure path
            $root = false === ($position = strpos($parameter, '[')) ? $parameter : substr($parameter, 0, $position);

            $request->attributes->remove($root);
            $request->query->remove($root);
            $request->request->remove($root);
        }

        return parent::onAuthenticationFailure($request, $exception);
    }
}
