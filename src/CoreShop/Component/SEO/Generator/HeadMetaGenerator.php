<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\SEO\Generator;

use CoreShop\Component\SEO\Model\SEOAwareInterface;
use CoreShop\Component\SEO\Model\SEOImageAwareInterface;
use CoreShop\Component\SEO\Model\SEOOpenGraphAwareInterface;
use Pimcore\Model\Asset\Image;
use Pimcore\Templating\Helper\Placeholder\Container;

class HeadMetaGenerator implements HeadMetaGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTitlePosition()
    {
        return Container::PREPEND;
    }

    /**
     * {@inheritdoc}
     */
    public function generateTitle(SEOAwareInterface $seoObject)
    {
        return $seoObject->getMetaTitle();
    }

    /**
     * {@inheritdoc}
     */
    public function generateDescription(SEOAwareInterface $seoObject)
    {
        return $seoObject->getMetaDescription();
    }

    /**
     * {@inheritdoc}
     */
    public function generateMeta(SEOAwareInterface $seoObject)
    {
        $params = [];

        if ($seoObject instanceof SEOOpenGraphAwareInterface) {
            $title = $this->generateTitle($seoObject);
            $description = $this->generateDescription($seoObject);
            $ogTitle = $seoObject->getOGTitle() ?: $title;
            $ogDescription = $seoObject->getOGDescription() ?: $description;
            $ogType = $seoObject->getOGType();
            $ogImage = NULL;

            if ($seoObject instanceof SEOImageAwareInterface && $seoObject->getImage() instanceof Image) {
                $ogImage = $seoObject->getImage()->getThumbnail('seo');
            }

            $params = [
                'og:title' => $ogTitle,
                'og:description' => $ogDescription,
                'og:image' => $ogImage,
                'og:type' => $ogType
            ];
        }

        return $params;
    }
}