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

namespace CoreShop\Bundle\CoreBundle\Dachcom\Seo;

use CoreShop\Component\Core\Model\SEOImageAwareInterface;
use CoreShop\Component\Core\Model\SEOOpenGraphAwareInterface;
use Pimcore\Model\Asset\Image;
use Pimcore\Tool;
use SeoBundle\MetaData\Extractor\ExtractorInterface;
use SeoBundle\Model\SeoMetaDataInterface;
use Webmozart\Assert\Assert;

final class OGExtractor implements ExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports($object)
    {
        return $object instanceof SEOOpenGraphAwareInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function updateMetadata($element, ?string $locale, SeoMetaDataInterface $seoMetadata)
    {
        Assert::isInstanceOf($element, SEOOpenGraphAwareInterface::class);

        if (!empty($element->getOGTitle($locale))) {
            $seoMetadata->addExtraProperty('og:title', $element->getOGTitle($locale));
        }

        if (!empty($element->getOGDescription($locale))) {
            $seoMetadata->addExtraProperty('og:description', $element->getOGDescription($locale));
        }

        if (!empty($element->getOGType())) {
            $seoMetadata->addExtraProperty('og:type', $element->getOGType());
        }

        if ($element instanceof SEOImageAwareInterface && $element->getImage() instanceof Image) {
            $ogImage = Tool::getHostUrl() . $element->getImage()->getThumbnail('seo');
            $seoMetadata->addExtraProperty('og:image', $ogImage);
        }
    }
}
