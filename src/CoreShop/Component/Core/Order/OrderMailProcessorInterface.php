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

namespace CoreShop\Component\Core\Order;

use CoreShop\Component\Order\Model\OrderInterface;
use Pimcore\Model\Document\Email;

interface OrderMailProcessorInterface
{
    public function sendOrderMail(Email $emailDocument, OrderInterface $order, bool $sendInvoices = false, bool $sendShipments = false, array $params = []): bool;
}
