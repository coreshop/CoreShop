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
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;

/**
 * Applies the same allow-list as the frontend controllers to the target URL of a successful login.
 *
 * Symfony accepts any "_target_path" (and, with use_referer, any Referer) that starts with "/" or
 * "http", which allows redirecting a freshly authenticated customer to a foreign host.
 */
class ShopUserAuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{
    use RedirectUrlValidationTrait;

    protected function determineTargetUrl(Request $request): string
    {
        $defaultTargetPath = $this->options['default_target_path'];

        return $this->validateRedirectUrl($request, parent::determineTargetUrl($request), $defaultTargetPath);
    }
}
