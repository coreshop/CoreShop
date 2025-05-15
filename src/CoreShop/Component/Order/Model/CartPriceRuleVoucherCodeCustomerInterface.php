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

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface CartPriceRuleVoucherCodeCustomerInterface extends ResourceInterface
{
    public function setVoucherCode(CartPriceRuleVoucherCodeInterface $voucherCode): void;

    public function getVoucherCode(): CartPriceRuleVoucherCodeInterface;

    public function getUses(): int;

    public function setUses(int $uses): void;

    public function incrementUses(): void;

    public function decrementUses(): void;

    public function getCustomerId(): int;

    public function setCustomerId(int $customerId): void;
}
