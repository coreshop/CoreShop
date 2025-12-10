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

namespace CoreShop\Bundle\ThemeBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\ThemeBundle\Service\CompositeThemeResolver;
use CoreShop\Bundle\ThemeBundle\Service\ThemeResolverInterface;
use CoreShop\Component\Registry\PrioritizedCompositeServicePass;

final class CompositeThemeResolverPass extends PrioritizedCompositeServicePass
{
    public const string THEME_RESOLVER_TAG = 'coreshop.theme.resolver';

    public function __construct(
        ) {
        parent::__construct(
            ThemeResolverInterface::class,
            CompositeThemeResolver::class,
            self::THEME_RESOLVER_TAG,
            'register',
        );
    }
}
