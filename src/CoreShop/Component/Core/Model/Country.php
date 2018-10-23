<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Address\Model\Country as BaseCountry;
use CoreShop\Component\Store\Model\StoresAwareTrait;
use Doctrine\Common\Collections\Collection;

class Country extends BaseCountry implements CountryInterface
{
    use StoresAwareTrait {
        __construct as storesAwareConstructor;
    }
    /**
     * @var CurrencyInterface
     */
    protected $currency;

    /**
     * @var Collection|StoreInterface[]
     */
    protected $stores;

    public function __construct()
    {
        parent::__construct();

        $this->storesAwareConstructor();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency(CurrencyInterface $currency = null)
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
