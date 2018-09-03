<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Order\Model\Order as BaseOrder;
use CoreShop\Component\Resource\ImplementedByPimcoreException;
use CoreShop\Component\Shipping\Model\CarrierAwareTrait;

class Order extends BaseOrder implements OrderInterface
{
    use CarrierAwareTrait;


    /**
     * {@inheritdoc}
     */
    public function getPaymentSettings()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentSettings($paymentSettings)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }
}
