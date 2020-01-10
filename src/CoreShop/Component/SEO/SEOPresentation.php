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

namespace CoreShop\Component\SEO;

use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\SEO\Extractor\ExtractorInterface;
use CoreShop\Component\SEO\Model\SEOMetadata;
use Pimcore\Templating\Helper\HeadMeta;
use Pimcore\Templating\Helper\HeadTitle;

class SEOPresentation implements SEOPresentationInterface
{
    /**
     * @var HeadMeta
     */
    protected $headMeta;

    /**
     * @var HeadTitle
     */
    protected $headTitle;

    /**
     * @var ServiceRegistryInterface
     */
    protected $extractorRegistry;

    /**
     * @param HeadMeta                 $headMeta
     * @param HeadTitle                $headTitle
     * @param ServiceRegistryInterface $extractorRegistry
     */
    public function __construct(HeadMeta $headMeta, HeadTitle $headTitle, ServiceRegistryInterface $extractorRegistry)
    {
        $this->headMeta = $headMeta;
        $this->headTitle = $headTitle;
        $this->extractorRegistry = $extractorRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function updateSeoMetadata($object)
    {
        $seoMetadata = $this->extractSeoMetaData($object);

        if ($extraProperties = $seoMetadata->getExtraProperties()) {
            foreach ($extraProperties as $key => $value) {
                $this->headMeta->appendProperty($key, $value);
            }
        }

        if ($extraNames = $seoMetadata->getExtraNames()) {
            foreach ($extraNames as $key => $value) {
                $this->headMeta->appendName($key, $value);
            }
        }

        if ($extraHttp = $seoMetadata->getExtraHttp()) {
            foreach ($extraHttp as $key => $value) {
                $this->headMeta->appendHttpEquiv($key, $value);
            }
        }

        if ($seoMetadata->getTitle()) {
            $this->headTitle->set($seoMetadata->getTitle());
        }

        if ($seoMetadata->getMetaDescription()) {
            $this->headMeta->setDescription($seoMetadata->getMetaDescription());
        }
    }

    /**
     * @param mixed $object
     *
     * @return SEOMetadata
     */
    protected function extractSeoMetaData($object)
    {
        $seoMetadata = new SEOMetadata();

        /**
         * @var ExtractorInterface $extractor
         */
        foreach ($this->extractorRegistry->all() as $extractor) {
            if ($extractor->supports($object)) {
                $extractor->updateMetadata($object, $seoMetadata);
            }
        }

        return $seoMetadata;
    }
}
