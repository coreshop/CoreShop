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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Order\Factory;

use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeCustomerInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;

class CartPriceRuleVoucherCodeCustomerFactory implements CartPriceRuleVoucherCodeCustomerFactoryInterface
{

    public function __construct(
        private FactoryInterface $voucherCodePerUserFactory,
    ) {
    }

    public function createNew()
    {
        return $this->voucherCodePerUserFactory->createNew();
    }

    public function createWithInitialData(CustomerInterface $customer, CartPriceRuleVoucherCodeInterface $voucherCode): CartPriceRuleVoucherCodeCustomerInterface
    {
        /**
         * @var CartPriceRuleVoucherCodeCustomerInterface $voucherCodeCustomer
         */
        $voucherCodeCustomer = $this->createNew();
        $voucherCodeCustomer->setCustomerId($customer->getId());
        $voucherCodeCustomer->setUses(1);
        $voucherCodeCustomer->setVoucherCode($voucherCode);

        return $voucherCodeCustomer;
    }

}
