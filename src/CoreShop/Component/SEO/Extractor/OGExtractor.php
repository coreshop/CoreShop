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

namespace CoreShop\Component\SEO\Extractor;

use CoreShop\Component\SEO\Model\SEOMetadataInterface;
use CoreShop\Component\SEO\Model\SEOOpenGraphAwareInterface;
use Webmozart\Assert\Assert;

final class OGExtractor implements ExtractorInterface
{
    public function supports($object): bool
    {
        return $object instanceof SEOOpenGraphAwareInterface;
    }

    public function updateMetadata($object, SEOMetadataInterface $seoMetadata): void
    {
        /**
         * @var SEOOpenGraphAwareInterface $object
         */
        Assert::isInstanceOf($object, SEOOpenGraphAwareInterface::class);

        if ($object->getOGTitle()) {
            $seoMetadata->addExtraProperty('og:title', $object->getOGTitle());
        }

        if ($object->getOGDescription()) {
            $seoMetadata->addExtraProperty('og:description', $object->getOGDescription());
        }

        if ($object->getOGType()) {
            $seoMetadata->addExtraProperty('og:type', $object->getOGType());
        }
    }
}
