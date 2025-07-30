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

namespace CoreShop\Component\Tracking\Extractor;

use CoreShop\Component\Registry\ServiceRegistryInterface;

class CompositeExtractor implements TrackingExtractorInterface
{
    public function __construct(
        private ServiceRegistryInterface $extractorRegistry,
    ) {
    }

    public function supports($object): bool
    {
        return true;
    }

    public function updateMetadata($object, $data = []): array
    {
        /**
         * @var TrackingExtractorInterface $extractor
         */
        foreach ($this->extractorRegistry->all() as $extractor) {
            if ($extractor->supports($object)) {
                $data = $extractor->updateMetadata($object, $data);
            }
        }

        return $data;
    }
}
