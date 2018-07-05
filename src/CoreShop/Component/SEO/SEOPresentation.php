<?php

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
     * @param $object
     *
     * @return SEOMetadata
     */
    protected function extractSeoMetaData($object)
    {
        $seoMetadata = new SEOMetadata();

        /**
         * @var ExtractorInterface
         */
        foreach ($this->extractorRegistry->all() as $extractor) {
            if ($extractor->supports($object)) {
                $extractor->updateMetadata($object, $seoMetadata);
            }
        }

        return $seoMetadata;
    }
}
