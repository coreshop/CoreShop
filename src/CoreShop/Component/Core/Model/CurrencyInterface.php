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

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Currency\Model\CurrencyInterface as BaseCurrencyInterface;
use Doctrine\Common\Collections\Collection;

interface CurrencyInterface extends BaseCurrencyInterface
{
    /**
     * @return Collection|CountryInterface[]
     */
    public function getCountries();

    /**
     * @return bool
     */
    public function hasCountries();

    public function addCountry(CountryInterface $country);

    public function removeCountry(CountryInterface $country);

    /**
     * @return bool
     */
    public function hasCountry(CountryInterface $country);
}
