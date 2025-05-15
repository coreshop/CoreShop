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
