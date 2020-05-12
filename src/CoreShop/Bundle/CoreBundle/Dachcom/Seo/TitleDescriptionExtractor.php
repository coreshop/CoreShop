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

use SeoBundle\MetaData\Extractor\ExtractorInterface;
use SeoBundle\Model\SeoMetaDataInterface;

final class TitleDescriptionExtractor implements ExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports($object)
    {
        return $object instanceof \CoreShop\Component\Core\Model\SEOAwareInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function updateMetadata($element, ?string $locale, SeoMetaDataInterface $seoMetadata)
    {
        if (method_exists($element, 'getMetaTitle') && !empty($element->getMetaTitle($locale))) {
            $seoMetadata->setTitle($element->getMetaTitle($locale));
        } elseif (method_exists($element, 'getName') && !empty($element->getName($locale))) {
            $seoMetadata->setTitle($element->getName($locale));
        }

        if (method_exists($element, 'getMetaDescription') && !empty($element->getMetaDescription($locale))) {
            $seoMetadata->setMetaDescription($element->getMetaDescription($locale));
        } elseif (method_exists($element, 'getShortDescription') && !empty($element->getShortDescription($locale))) {
            $seoMetadata->setMetaDescription($element->getShortDescription($locale));
        }
    }
}
