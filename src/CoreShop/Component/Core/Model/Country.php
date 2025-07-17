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

use CoreShop\Component\Address\Model\Country as BaseCountry;
use CoreShop\Component\Store\Model\StoresAwareTrait;

/**
 * @psalm-suppress MissingConstructor
 */
class Country extends BaseCountry implements CountryInterface
{
    use StoresAwareTrait {
        __construct as storesAwareConstructor;
    }

    protected ?CurrencyInterface $currency = null;

    public function __construct(
        ) {
        parent::__construct();

        $this->storesAwareConstructor();
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency(?CurrencyInterface $currency)
    {
        $this->currency = $currency;

        if (null !== $currency) {
            $currency->addCountry($this);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s', $this->getIsoCode());
    }
}
