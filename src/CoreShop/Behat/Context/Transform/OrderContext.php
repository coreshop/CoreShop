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

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\OrderShipmentInterface;
use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Component\Order\Model\OrderPaymentInterface;

final class OrderContext implements Context
{
    private SharedStorageInterface $sharedStorage;

    public function __construct(SharedStorageInterface $sharedStorage)
    {
        $this->sharedStorage = $sharedStorage;
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
