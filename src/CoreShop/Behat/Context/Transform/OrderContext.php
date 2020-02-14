<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;

final class OrderContext implements Context
{
    private $sharedStorage;

    public function __construct(SharedStorageInterface $sharedStorage)
    {
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Transform /^the order/
     * @Transform /^my order/
     */
    public function order()
    {
        return $this->sharedStorage->get('order');
    }

    /**
     * @Transform /^latest order invoice/
     */
    public function latestOrderInvoice()
    {
        return $this->sharedStorage->get('orderInvoice');
    }

    /**
     * @Transform /^latest order shipment/
     */
    public function latestOrderShipment()
    {
        return $this->sharedStorage->get('orderShipment');
    }

    /**
     * @Transform /^latest order payment/
     */
    public function latestOrderPayment()
    {
        return $this->sharedStorage->get('orderPayment');
    }
}
