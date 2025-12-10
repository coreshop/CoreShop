<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\FrontendBundle\DependencyInjection\CompilerPass;

use CoreShop\Component\Registry\RegisterSimpleRegistryTypePass;

final class FrontendInstallerPass extends RegisterSimpleRegistryTypePass
{
    public const FRONTEND_INSTALLER_TAG = 'coreshop.frontend.installer';

    public function __construct(
        ) {
        parent::__construct(
            'coreshop.registry.frontend.installers',
            'coreshop.frontend.installers',
            self::FRONTEND_INSTALLER_TAG,
        );
    }
}
