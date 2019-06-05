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

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Payment\Model\Payment as BasePayment;
use Webmozart\Assert\Assert;

class Payment extends BasePayment implements PaymentInterface
{
    /**
     * @var OrderInterface
     */
    protected $order;

    /**
     * @var CurrencyInterface
     */
    protected $currency;

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder(\CoreShop\Component\Order\Model\OrderInterface $order)
    {
        Assert::isInstanceOf($order, OrderInterface::class);

        $this->order = $order;
        $this->orderId = $order->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency($currency)
    {
        $this->currencyCode = $currency->getIsoCode();
        $this->currency = $currency;
    }
}
