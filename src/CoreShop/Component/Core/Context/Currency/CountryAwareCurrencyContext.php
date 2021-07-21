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

declare(strict_types=1);

namespace CoreShop\Component\Core\Context\Currency;

use CoreShop\Component\Address\Context\CountryContextInterface;
use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Currency\Context\CurrencyNotFoundException;
use CoreShop\Component\Currency\Model\CurrencyInterface;

final class CountryAwareCurrencyContext implements CurrencyContextInterface
{
    private CountryContextInterface $countryContext;

    public function __construct(CountryContextInterface $countryContext)
    {
        $this->countryContext = $countryContext;
    }

    public function getCurrency(): CurrencyInterface
    {
        /** @var CountryInterface $country */
        $country = $this->countryContext->getCountry();

        if (null === $country) {
            throw new CurrencyNotFoundException();
        }

        return $country->getCurrency();
    }
}
