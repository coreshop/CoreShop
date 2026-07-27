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

namespace CoreShop\Bundle\SEOBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterSimpleRegistryTypePass;

final class ExtractorRegistryServicePass extends RegisterSimpleRegistryTypePass
{
    public const string EXTRACTOR_TAG = 'coreshop.seo.extractor';

    public function __construct(
        ) {
        parent::__construct(
            'coreshop.registry.seo.extractor',
            'coreshop.seo.extractors',
            self::EXTRACTOR_TAG,
        );
    }
}
