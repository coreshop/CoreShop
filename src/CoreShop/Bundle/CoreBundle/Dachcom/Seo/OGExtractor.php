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

use Pimcore\Tool;
use SeoBundle\MetaData\Extractor\ExtractorInterface;
use SeoBundle\Model\SeoMetaDataInterface;

final class OGExtractor implements ExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports($object)
    {
        return $object instanceof \CoreShop\Component\Core\Model\SEOOpenGraphAwareInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function updateMetadata($element, ?string $locale, SeoMetaDataInterface $seoMetadata)
    {
        if (method_exists($element, 'getMetaTitle') && !empty($element->getOGTitle($locale))) {
            $seoMetadata->addExtraProperty('og:title', $element->getOGTitle($locale));
        } elseif (method_exists($element, 'getName') && !empty($element->getName($locale))) {
            $seoMetadata->addExtraProperty('og:title', $element->getName($locale));
        }

        if (method_exists($element, 'getOGDescription') && !empty($element->getOGDescription($locale))) {
            $seoMetadata->addExtraProperty('og:description', $element->getOGDescription($locale));
        } elseif (method_exists($element, 'getShortDescription') && !empty($element->getShortDescription($locale))) {
            $seoMetadata->addExtraProperty('og:description', $element->getShortDescription($locale));
        }

        if (method_exists($element, 'getOGType') && !empty($element->getOGType())) {
            $seoMetadata->addExtraProperty('og:type', $element->getOGType());
        }

        if (method_exists($element, 'getImage') && !empty($element->getImage())) {
            $ogImage = Tool::getHostUrl() . $element->getImage()->getThumbnail('seo');
            $seoMetadata->addExtraProperty('og:image', $ogImage);
        }
    }
}
