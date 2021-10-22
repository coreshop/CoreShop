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
