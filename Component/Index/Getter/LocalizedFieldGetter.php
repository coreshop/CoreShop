<?php

namespace CoreShop\Component\Index\Getter;

use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use Pimcore\Service\Locale;

class LocalizedFieldGetter implements GetterInterface
{
    /**
     * @var Locale
     */
    protected $localeService;

    /**
     * @param Locale $localeService
     */
    public function __construct(Locale $localeService)
    {
        $this->localeService = $localeService;
    }

    /**
     * {@inheritdoc}
     */
    public function get(PimcoreModelInterface $object, IndexColumnInterface $config)
    {
        $language = null;

        if ($this->localeService->getLocale()) {
            $language = $this->localeService->getLocale();
        }

        $getter = 'get'.ucfirst($config->getConfiguration()['key']);

        return $object->$getter($language);
    }
}
