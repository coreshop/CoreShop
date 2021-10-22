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

use CoreShop\Component\SEO\Model\SEOAwareInterface;
use CoreShop\Component\SEO\Model\SEOMetadataInterface;
use Webmozart\Assert\Assert;

final class TitleExtractor implements ExtractorInterface
{
    public function supports($object): bool
    {
        return $object instanceof SEOAwareInterface || method_exists($object, 'getMetaTitle');
    }

    public function updateMetadata($object, SEOMetadataInterface $seoMetadata): void
    {
        /*
         * @var SEOAwareInterface $object
         */
        Assert::isInstanceOf($object, SEOAwareInterface::class);

        $seoMetadata->setTitle($object->getMetaTitle());
    }
}
