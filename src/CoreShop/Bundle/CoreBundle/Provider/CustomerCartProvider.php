<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Provider;

use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Customer\Context\CustomerNotFoundException;
use CoreShop\Component\Order\Repository\CartRepositoryInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;

final class CustomerCartProvider implements CustomerCartProviderInterface
{
    /**
     * @var CustomerContextInterface
     */
    protected $customerContext;

    /**
     * @var StoreContextInterface
     */
    protected $storeContext;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @param CustomerContextInterface $customerContext
     * @param StoreContextInterface    $storeContext
     * @param CartRepositoryInterface  $cartRepository
     */
    public function __construct(
        CustomerContextInterface $customerContext,
        StoreContextInterface $storeContext,
        CartRepositoryInterface $cartRepository
    ) {
        $this->customerContext = $customerContext;
        $this->storeContext = $storeContext;
        $this->cartRepository = $cartRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function provide()
    {
        try {
            $store = $this->storeContext->getStore();
        } catch (StoreNotFoundException $exception) {
            return null;
        }

        try {
            $customer = $this->customerContext->getCustomer();
        } catch (CustomerNotFoundException $exception) {
            return null;
        }

        $cart = $this->cartRepository->findLatestByStoreAndCustomer($store, $customer);

        return $cart;
    }
}
