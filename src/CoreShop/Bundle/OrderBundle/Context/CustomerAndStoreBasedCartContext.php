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

namespace CoreShop\Bundle\OrderBundle\Context;

use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Customer\Context\CustomerNotFoundException;
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Order\Context\CartNotFoundException;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;
use Pimcore\Http\RequestHelper;

final class CustomerAndStoreBasedCartContext implements CartContextInterface
{
    public function __construct(private CustomerContextInterface $customerContext, private StoreContextInterface $storeContext, private OrderRepositoryInterface $cartRepository, private RequestHelper $pimcoreRequestHelper)
    {
    }

    public function getCart(): OrderInterface
    {
        /**
         * @psalm-suppress DeprecatedMethod
         */
        if (
            $this->pimcoreRequestHelper->hasMasterRequest() &&
            $this->pimcoreRequestHelper->getMasterRequest()->get('_route') !== 'coreshop_login_check'
        ) {
            throw new CartNotFoundException('CustomerAndStoreBasedCartContext can only be applied in coreshop_login_check route.');
        }

        try {
            $store = $this->storeContext->getStore();
        } catch (StoreNotFoundException) {
            throw new CartNotFoundException('CoreShop was not able to find the cart, as there is no current store.');
        }

        try {
            $customer = $this->customerContext->getCustomer();
        } catch (CustomerNotFoundException) {
            throw new CartNotFoundException('CoreShop was not able to find the cart, as there is no logged in user.');
        }

        $cart = $this->cartRepository->findLatestCartByStoreAndCustomer($store, $customer);
        if (null === $cart) {
            throw new CartNotFoundException('CoreShop was not able to find the cart for currently logged in user.');
        }

        return $cart;
    }
}
