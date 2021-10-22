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

namespace CoreShop\Component\Core\Context\Currency;

use CoreShop\Component\Address\Context\CountryContextInterface;
use CoreShop\Component\Address\Context\CountryNotFoundException;
use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Currency\Context\CurrencyNotFoundException;
use CoreShop\Component\Currency\Model\CurrencyInterface;

final class CountryAwareCurrencyContext implements CurrencyContextInterface
{
    public function __construct(private CountryContextInterface $countryContext)
    {
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
