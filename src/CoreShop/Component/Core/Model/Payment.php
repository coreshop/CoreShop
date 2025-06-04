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

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Payment\Model\Payment as BasePayment;
use Webmozart\Assert\Assert;

/**
 * @psalm-suppress MissingConstructor
 */
class Payment extends BasePayment implements PaymentInterface
{
    protected ?OrderInterface $order = null;

    protected ?CurrencyInterface $currency = null;

    public function getOrder(): ?OrderInterface
    {
        return $this->order;
    }

    public function setOrder(\CoreShop\Component\Order\Model\OrderInterface $order)
    {
        /**
         * @var OrderInterface $order
         */
        Assert::isInstanceOf($order, OrderInterface::class);

        $this->order = $order;
        $this->orderId = $order->getId();
    }

    public function getCurrency(): ?CurrencyInterface
    {
        return $this->currency;
    }

    public function setCurrency($currency)
    {
        $this->currencyCode = $currency->getIsoCode();
        $this->currency = $currency;
    }
}
