<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\Locale\Context;

final class LocaleNotFoundException extends \RuntimeException
{
    public function __construct(
        $message = null,
        ?\Exception $previousException = null,
    ) {
        parent::__construct($message ?: 'Locale could not be found!', 0, $previousException);
    }

    public static function notFound(string $localeCode): self
    {
        return new self(sprintf('Locale "%s" cannot be found!', $localeCode));
    }

    public static function notAvailable(string $localeCode, array $availableLocalesCodes): self
    {
        return new self(sprintf(
            'Locale "%s" is not available! The available ones are: "%s".',
            $localeCode,
            implode('", "', $availableLocalesCodes),
        ));
    }
}
