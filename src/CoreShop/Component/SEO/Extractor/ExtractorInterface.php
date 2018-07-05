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

namespace CoreShop\Component\SEO\Extractor;

use CoreShop\Component\SEO\Model\SEOMetadataInterface;

interface ExtractorInterface
{
    /**
     * Check whether the strategy supports this content.
     *
     * The decision could be based on the content implementing
     * an interface or being instance of a specific class,
     * or introspection to see if a certain method exists.
     *
     * @param object $object
     *
     * @return bool
     */
    public function supports($object);

    /**
     * Update the metadata with information from this content.
     *
     * It is up to the strategy to check if certain fields
     * are already set by previous strategies and decide on a merge strategy.
     *
     * This method should only be called if supports returned true.
     *
     * @param object               $object
     * @param SEOMetadataInterface $seoMetadata
     */
    public function updateMetadata($object, SEOMetadataInterface $seoMetadata);
}
