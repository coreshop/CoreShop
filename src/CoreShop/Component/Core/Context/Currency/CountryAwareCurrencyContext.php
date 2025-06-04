<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Core\Context\Currency;

use CoreShop\Component\Address\Context\CountryContextInterface;
use CoreShop\Component\Address\Context\CountryNotFoundException;
use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Currency\Context\CurrencyNotFoundException;
use CoreShop\Component\Currency\Model\CurrencyInterface;

final class CountryAwareCurrencyContext implements CurrencyContextInterface
{
    public function __construct(
        private CountryContextInterface $countryContext,
    ) {
    }

    public function getCurrency(): CurrencyInterface
    {
        try {
            /** @var CountryInterface $country */
            $country = $this->countryContext->getCountry();
        } catch (CountryNotFoundException $ex) {
            throw new CurrencyNotFoundException(null, $ex);
        }

        return $country->getCurrency();
    }
}
