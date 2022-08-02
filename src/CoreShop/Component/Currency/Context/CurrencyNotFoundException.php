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

namespace CoreShop\Component\Currency\Context;

final class CurrencyNotFoundException extends \RuntimeException
{
    public function __construct($message = null, \Exception $previousException = null)
    {
        parent::__construct($message ?: 'Currency could not be found!', 0, $previousException);
    }

    /**
     * @param string $currencyCode
     */
    public static function notFound($currencyCode): self
    {
        return new self(sprintf('Currency "%s" cannot be found!', $currencyCode));
    }

    /**
     * @param string $currencyCode
     */
    public static function disabled($currencyCode): self
    {
        return new self(sprintf('Currency "%s" is disabled!', $currencyCode));
    }

    /**
     * @param string $currencyCode
     */
    public static function notAvailable($currencyCode, array $availableCurrenciesCodes): self
    {
        return new self(sprintf(
            'Currency "%s" is not available! The available ones are: "%s".',
            $currencyCode,
            implode('", "', $availableCurrenciesCodes)
        ));
    }
}
