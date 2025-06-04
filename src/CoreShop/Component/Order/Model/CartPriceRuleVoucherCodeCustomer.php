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

use CoreShop\Component\Resource\Model\SetValuesTrait;

/**
 * @psalm-suppress MissingConstructor
 */
class CartPriceRuleVoucherCodeCustomer implements CartPriceRuleVoucherCodeCustomerInterface
{
    use SetValuesTrait;

    /**
     * @var int
     */
    protected $id;

    /** @var \CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface */
    protected $voucherCode;

    protected int $customerId;

    protected int $uses;

    public function getId(): int
    {
        return $this->id;
    }

    public function getVoucherCode(): CartPriceRuleVoucherCodeInterface
    {
        return $this->voucherCode;
    }

    public function setVoucherCode($voucherCode): void
    {
        $this->voucherCode = $voucherCode;
    }

    public function getUses(): int
    {
        return $this->uses;
    }

    public function setUses(int $uses): void
    {
        $this->uses = $uses;
    }

    public function incrementUses(): void
    {
        ++$this->uses;
    }

    public function decrementUses(): void
    {
        --$this->uses;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): void
    {
        $this->customerId = $customerId;
    }
}
