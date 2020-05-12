<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\SEO\Extractor;

use CoreShop\Component\SEO\Model\SEOMetadataInterface;
use CoreShop\Component\SEO\Model\SEOOpenGraphAwareInterface;
use Webmozart\Assert\Assert;

/**
 * @deprecated Deprecated due to https://github.com/dachcom-digital/pimcore-seo, will be removed with 3.1
 */
final class OGExtractor implements ExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports($object): bool
    {
        return $object instanceof SEOOpenGraphAwareInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function updateMetadata($object, SEOMetadataInterface $seoMetadata): void
    {
        Assert::isInstanceOf($object, SEOOpenGraphAwareInterface::class);

        /**
         * @var $object SEOOpenGraphAwareInterface
         */
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
