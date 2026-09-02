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

namespace CoreShop\Bundle\LocaleBundle\DependencyInjection\Compiler;

use CoreShop\Component\Locale\Context\CompositeLocaleContext;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Registry\PrioritizedCompositeServicePass;

final class CompositeLocaleContextPass extends PrioritizedCompositeServicePass
{
    public const string LOCALE_CONTEXT_SERVICE_TAG = 'coreshop.context.locale';

    public function __construct(
        ) {
        parent::__construct(
            LocaleContextInterface::class,
            CompositeLocaleContext::class,
            self::LOCALE_CONTEXT_SERVICE_TAG,
            'addContext',
        );
    }
}
