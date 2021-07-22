<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Order;

use CoreShop\Component\Order\Model\OrderInterface;
use Pimcore\Model\Document\Email;

interface OrderMailProcessorInterface
{
    /**
     * @param Email          $emailDocument
     * @param OrderInterface $order
     * @param bool           $sendInvoices
     * @param bool           $sendShipments
     * @param array          $params
     */
    public function sendOrderMail($emailDocument, OrderInterface $order, $sendInvoices = false, $sendShipments = false, $params = []);
}
