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

namespace CoreShop\Bundle\ProductQuantityPriceRulesBundle\Studio;

use Pimcore\Bundle\StudioUiBundle\Build\BuildArchive;
use Pimcore\Bundle\StudioUiBundle\Build\BuildArchiveExtractionTrait;
use Pimcore\Bundle\StudioUiBundle\Build\BuildArchiveProviderInterface;

/**
 * The Studio build of this bundle ships as a single archive in Resources/build-dist and is
 * extracted into Resources/public/studio at cache warmup (or on first use while the
 * directory is writable) by Pimcore's BuildArchiveExtractor.
 *
 * @internal
 */
final class WebpackEntryPointProvider implements BuildArchiveProviderInterface
{
    use BuildArchiveExtractionTrait;

    protected function buildArchive(): BuildArchive
    {
        return new BuildArchive(
            archiveGlob: __DIR__ . '/../Resources/build-dist/build*.zip',
            targetDir: __DIR__ . '/../Resources/public/studio',
        );
    }

    public function getEntryPoints(): array
    {
        return ['exposeRemote'];
    }

    public function getOptionalEntryPoints(): array
    {
        return [];
    }
}