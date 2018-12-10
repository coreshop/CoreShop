<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\SEO\Extractor;

use CoreShop\Component\SEO\Model\LuceneSearchAwareInterface;
use CoreShop\Component\SEO\Model\LuceneSearchCategoriesAwareInterface;
use CoreShop\Component\SEO\Model\SEOMetadataInterface;
use LuceneSearchBundle\Tool\CrawlerState;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Document;
use Pimcore\Model\Element\AbstractElement;

final class LuceneSearchExtractor implements ExtractorInterface
{
    /**
     * @var CrawlerState
     */
    private $crawlerState;

    /**
     * @param CrawlerState $crawlerState
     */
    public function __construct(CrawlerState $crawlerState)
    {
        $this->crawlerState = $crawlerState;
    }

    public function supports($object)
    {
        return $object instanceof LuceneSearchAwareInterface ||
            $object instanceof LuceneSearchCategoriesAwareInterface ||
            $object instanceof AbstractElement;
    }

    public function updateMetadata($object, SEOMetadataInterface $seoMetadata)
    {
        if (!class_exists(CrawlerState::class)) {
            throw new \LogicException(
                'You can not use the "' . self::class . '" class if the Lucene Search is not available. Try running "composer require dachcom-digital/lucene-bundle".'
            );
        }

        if (!$this->crawlerState->isLuceneSearchCrawler()) {
            return;
        }

        if ($object instanceof LuceneSearchAwareInterface) {
            $seoMetadata->addExtraName('lucene-search:boost', $object->getLuceneSearchBoost());
        }

        if ($object instanceof LuceneSearchCategoriesAwareInterface) {
            $objectCategories = $object->getLuceneSearchCategories();

            if (is_array($objectCategories)) {
                $objectCategories = implode(',', $objectCategories);
            }

            $seoMetadata->addExtraName('lucene-search:categories', $objectCategories);
        }

        if ($object instanceof Concrete) {
            $seoMetadata->addExtraName('lucene-search:objectId', $object->getId());
        }

        if ($object instanceof Document) {
            $seoMetadata->addExtraName('lucene-search:documentId', $object->getId());
        }

        if ($object instanceof AbstractElement) {
            $properties = $object->getProperties();
            $possibleProperties = ['lucene-search:boost', 'lucene-search:categories'];

            foreach ($possibleProperties as $property) {
                if (!array_key_exists($property, $properties)) {
                    continue;
                }

                $seoMetadata->addExtraName($property, $properties[$property]->getData());
            }
        }
    }
}
