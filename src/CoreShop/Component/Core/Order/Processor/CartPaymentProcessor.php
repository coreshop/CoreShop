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

namespace CoreShop\Component\Core\Order\Processor;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;

final class CartPaymentProcessor implements CartProcessorInterface
{
    public function __construct(private int $decimalFactor, private int $decimalPrecision)
    {
    }

    public function process(OrderInterface $cart): void
    {
        $cart->setPaymentTotal(
            (int)round((round($cart->getTotal() / $this->decimalFactor, $this->decimalPrecision) * 100), 0)
        );
    }
}
