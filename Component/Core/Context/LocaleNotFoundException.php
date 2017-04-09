<?php

namespace CoreShop\Component\Core\Context;

final class LocaleNotFoundException extends \RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct($message = null, \Exception $previousException = null)
    {
        parent::__construct($message ?: 'Locale could not be found!', 0, $previousException);
    }

    /**
     * @param string $localeCode
     *
     * @return self
     */
    public static function notFound($localeCode)
    {
        return new self(sprintf('Locale "%s" cannot be found!', $localeCode));
    }

    /**
     * @param string $localeCode
     * @param array $availableLocalesCodes
     *
     * @return self
     */
    public static function notAvailable($localeCode, array $availableLocalesCodes)
    {
        return new self(sprintf(
            'Locale "%s" is not available! The available ones are: "%s".',
            $localeCode,
            implode('", "', $availableLocalesCodes)
        ));
    }
}
