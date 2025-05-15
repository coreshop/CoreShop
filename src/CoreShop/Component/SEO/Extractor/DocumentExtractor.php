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
use Pimcore\Model\Document\Page;
use Webmozart\Assert\Assert;

final class DocumentExtractor implements ExtractorInterface
{
    public function supports($object): bool
    {
        return $object instanceof Page;
    }

    public function updateMetadata($object, SEOMetadataInterface $seoMetadata): void
    {
        /**
         * @var Page $object
         */
        Assert::isInstanceOf($object, Page::class);

        if ($object->getTitle()) {
            $seoMetadata->setTitle($object->getTitle());
        }

        if ($object->getDescription()) {
            $seoMetadata->setMetaDescription($object->getDescription());
        }
    }
}
