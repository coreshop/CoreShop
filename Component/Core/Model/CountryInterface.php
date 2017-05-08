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
 *
*/

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Address\Model\CountryInterface as BaseCountryInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use Doctrine\Common\Collections\Collection;

interface CountryInterface extends BaseCountryInterface
{
    /**
     * @return CurrencyInterface
     */
    public function getCurrency();

    /**
     * @param CurrencyInterface $currency
     *
     * @return static
     */
    public function setCurrency($currency);

    /**
     * @return Collection|StoreInterface[]
     */
    public function getStores();

    /**
     * @return bool
     */
    public function hasStores();

    /**
     * @param StoreInterface $store
     */
    public function addStore(StoreInterface $store);

    /**
     * @param StoreInterface $store
     */
    public function removeStore(StoreInterface $store);

    /**
     * @param StoreInterface $store
     *
     * @return bool
     */
    public function hasStore(StoreInterface $store);
}
