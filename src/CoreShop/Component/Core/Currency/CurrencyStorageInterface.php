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

namespace CoreShop\Component\Core\Currency;

use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Currency\Context\CurrencyNotFoundException;
use CoreShop\Component\Store\Model\StoreInterface;

interface CurrencyStorageInterface
{
    /**
     * @param StoreInterface    $store
     * @param CurrencyInterface $currency
     */
    public function set(StoreInterface $store, CurrencyInterface $currency): void;

    /**
     * @param StoreInterface $store
     *
     * @return CurrencyInterface
     *
     * @throws CurrencyNotFoundException
     */
    public function get(StoreInterface $store): CurrencyInterface;
}
