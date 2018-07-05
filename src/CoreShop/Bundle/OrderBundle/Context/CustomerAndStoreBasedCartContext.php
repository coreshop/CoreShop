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

namespace CoreShop\Bundle\OrderBundle\Context;

use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Customer\Context\CustomerNotFoundException;
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Order\Context\CartNotFoundException;
use CoreShop\Component\Order\Repository\CartRepositoryInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;
use Pimcore\Http\RequestHelper;

final class CustomerAndStoreBasedCartContext implements CartContextInterface
{
    /**
     * @var CustomerContextInterface
     */
    private $customerContext;

    /**
     * @var StoreContextInterface
     */
    private $storeContext;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var RequestHelper
     */
    private $pimcoreRequestHelper;

    /**
     * @param CustomerContextInterface $customerContext
     * @param StoreContextInterface    $storeContext
     * @param CartRepositoryInterface  $cartRepository
     * @param RequestHelper            $pimcoreRequestHelper
     */
    public function __construct(
        CustomerContextInterface $customerContext,
        StoreContextInterface $storeContext,
        CartRepositoryInterface $cartRepository,
        RequestHelper $pimcoreRequestHelper
    ) {
        $this->customerContext = $customerContext;
        $this->storeContext = $storeContext;
        $this->cartRepository = $cartRepository;
        $this->pimcoreRequestHelper = $pimcoreRequestHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getCart()
    {
        if ($this->pimcoreRequestHelper->hasMasterRequest()) {
            if ('coreshop_login_check' !== $this->pimcoreRequestHelper->getMasterRequest()->get('_route')) {
                throw new CartNotFoundException('CustomerAndStoreBasedCartContext can only be applied in coreshop_login_check route.');
            }
        }

        try {
            $store = $this->storeContext->getStore();
        } catch (StoreNotFoundException $exception) {
            throw new CartNotFoundException('CoreShop was not able to find the cart, as there is no current store.');
        }

        try {
            $customer = $this->customerContext->getCustomer();
        } catch (CustomerNotFoundException $exception) {
            throw new CartNotFoundException('CoreShop was not able to find the cart, as there is no logged in user.');
        }

        $cart = $this->cartRepository->findLatestByStoreAndCustomer($store, $customer);
        if (null === $cart) {
            throw new CartNotFoundException('CoreShop was not able to find the cart for currently logged in user.');
        }

        return $cart;
    }
}
