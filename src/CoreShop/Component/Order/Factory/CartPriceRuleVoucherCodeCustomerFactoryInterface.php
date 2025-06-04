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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Order\Factory;

use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeCustomerInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;

interface CartPriceRuleVoucherCodeCustomerFactoryInterface extends FactoryInterface
{
    public function createWithInitialData(CustomerInterface $customer, CartPriceRuleVoucherCodeInterface $voucherCode): CartPriceRuleVoucherCodeCustomerInterface;
}
