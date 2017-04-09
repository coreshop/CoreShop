<?php

namespace CoreShop\Component\Core\Context;

use Pimcore\Service\Locale;

class PimcoreLocaleContext implements LocaleContextInterface
{
    /**
     * @var Locale
     */
    private $pimcoreLocaleService;

    /**
     * @param Locale $pimcoreLocaleService
     */
    public function __construct(Locale $pimcoreLocaleService)
    {
        $this->pimcoreLocaleService = $pimcoreLocaleService;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocaleCode() {
        return $this->pimcoreLocaleService->getLocale();
    }
}
