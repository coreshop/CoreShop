<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Bundle\TestBundle\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\OrderShipmentInterface;
use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Component\Order\Model\OrderPaymentInterface;

final class OrderContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @Transform /^the order/
     * @Transform /^my order/
     */
    public function order(): OrderInterface
    {
        return $this->sharedStorage->get('order');
    }

    /**
     * @Transform /^latest order invoice/
     */
    public function latestOrderInvoice(): OrderInvoiceInterface
    {
        return $this->sharedStorage->get('orderInvoice');
    }

    /**
     * @Transform /^latest order shipment/
     */
    public function latestOrderShipment(): OrderShipmentInterface
    {
        return $this->sharedStorage->get('orderShipment');
    }

    /**
     * @Transform /^latest order payment/
     */
    public function latestOrderPayment(): OrderPaymentInterface
    {
        return $this->sharedStorage->get('orderPayment');
    }
}
