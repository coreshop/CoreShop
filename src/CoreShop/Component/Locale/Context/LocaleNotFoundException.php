<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Locale\Context;

final class LocaleNotFoundException extends \RuntimeException
{
    public function __construct($message = null, \Exception $previousException = null)
    {
        parent::__construct($message ?: 'Locale could not be found!', 0, $previousException);
    }

    /**
     * @param string $localeCode
     *
     * @return self
     */
    public static function notFound($localeCode): LocaleNotFoundException
    {
        return new self(sprintf('Locale "%s" cannot be found!', $localeCode));
    }

    /**
     * @param string $localeCode
     * @param array  $availableLocalesCodes
     *
     * @return self
     */
    public static function notAvailable($localeCode, array $availableLocalesCodes): LocaleNotFoundException
    {
        return new self(sprintf(
            'Locale "%s" is not available! The available ones are: "%s".',
            $localeCode,
            implode('", "', $availableLocalesCodes)
        ));
    }
}
