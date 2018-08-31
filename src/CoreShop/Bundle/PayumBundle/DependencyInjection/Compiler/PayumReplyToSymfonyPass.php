<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\PayumBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\PayumBundle\Exception\ReplyToSymfonyResponseConverter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class PayumReplyToSymfonyPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definitionId = 'payum.converter.reply_to_http_response';

        if (!$container->has($definitionId)) {
            return;
        }

        $container->findDefinition($definitionId)->setClass(ReplyToSymfonyResponseConverter::class);
    }
}
