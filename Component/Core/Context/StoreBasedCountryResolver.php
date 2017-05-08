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

namespace CoreShop\Component\Core\Context;

use CoreShop\Component\Address\Context\CountryNotFoundException;
use CoreShop\Component\Address\Context\RequestBased\RequestResolverInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use Symfony\Component\HttpFoundation\Request;

final class StoreBasedCountryResolver implements RequestResolverInterface
{
    /**
     * @var StoreContextInterface
     */
    private $storeContext;

    public function __construct(StoreContextInterface $storeContext)
    {
        $this->storeContext = $storeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function findCountry(Request $request)
    {
        $store = $this->storeContext->getStore();

        if ($store instanceof StoreInterface)
            return $store->getBaseCountry();

        throw new CountryNotFoundException();
    }
}
