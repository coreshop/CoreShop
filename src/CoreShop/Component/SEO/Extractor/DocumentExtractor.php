<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

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
        /*
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
