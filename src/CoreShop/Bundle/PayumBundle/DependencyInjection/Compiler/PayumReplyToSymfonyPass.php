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

namespace CoreShop\Bundle\PayumBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\PayumBundle\Exception\ReplyToSymfonyResponseConverter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class PayumReplyToSymfonyPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $definitionId = 'payum.converter.reply_to_http_response';

        if (!$container->has($definitionId)) {
            return;
        }

        $container->findDefinition($definitionId)->setClass(ReplyToSymfonyResponseConverter::class);
    }
}
