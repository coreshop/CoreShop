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

namespace CoreShop\Component\Currency\Context;

final class CurrencyNotFoundException extends \RuntimeException
{
    public function __construct(
        $message = null,
        ?\Exception $previousException = null,
    ) {
        parent::__construct($message ?: 'Currency could not be found!', 0, $previousException);
    }

    public static function notFound(string $currencyCode): self
    {
        return new self(sprintf('Currency "%s" cannot be found!', $currencyCode));
    }

    public static function disabled(string $currencyCode): self
    {
        return new self(sprintf('Currency "%s" is disabled!', $currencyCode));
    }

    public static function notAvailable(string $currencyCode, array $availableCurrenciesCodes): self
    {
        return new self(sprintf(
            'Currency "%s" is not available! The available ones are: "%s".',
            $currencyCode,
            implode('", "', $availableCurrenciesCodes),
        ));
    }
}
