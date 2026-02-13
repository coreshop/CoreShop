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

namespace CoreShop\Bundle\StudioFormBundle;

use CoreShop\Bundle\StudioFormBundle\DependencyInjection\Compiler\RegisterFormSchemaEnricherPass;
use CoreShop\Bundle\StudioFormBundle\DependencyInjection\Compiler\RegisterStudioFormTypesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class CoreShopStudioFormBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterFormSchemaEnricherPass());
        $container->addCompilerPass(new RegisterStudioFormTypesPass());
    }
}
