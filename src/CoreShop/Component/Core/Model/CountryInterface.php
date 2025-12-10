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

use CoreShop\Component\Address\Model\CountryInterface as BaseCountryInterface;
use CoreShop\Component\Store\Model\StoresAwareInterface;

interface CountryInterface extends BaseCountryInterface, StoresAwareInterface
{
    /**
     * @return CurrencyInterface|null
     */
    public function getCurrency();

    public function setCurrency(?CurrencyInterface $currency);
}
