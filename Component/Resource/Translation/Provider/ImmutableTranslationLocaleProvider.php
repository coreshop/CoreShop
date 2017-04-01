<?php

namespace CoreShop\Component\Resource\Translation\Provider;

final class ImmutableTranslationLocaleProvider implements TranslationLocaleProviderInterface
{
    /**
     * @var array
     */
    private $definedLocalesCodes;

    /**
     * @var string
     */
    private $defaultLocaleCode;

    /**
     * @param array $definedLocalesCodes
     * @param string $defaultLocaleCode
     */
    public function __construct(array $definedLocalesCodes, $defaultLocaleCode)
    {
        $this->definedLocalesCodes = $definedLocalesCodes;
        $this->defaultLocaleCode = $defaultLocaleCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinedLocalesCodes()
    {
        return $this->definedLocalesCodes;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLocaleCode()
    {
        return $this->defaultLocaleCode;
    }
}
