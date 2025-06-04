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

namespace CoreShop\Component\Order\Model;

use Carbon\Carbon;

interface OrderShipmentInterface extends OrderDocumentInterface
{
    public function getShipmentDate(): ?Carbon;

    public function setShipmentDate(?Carbon $shipmentDate);

    public function getShipmentNumber(): ?string;

    public function setShipmentNumber(?string $shipmentNumber);

    public function getTrackingCode(): ?string;

    public function setTrackingCode(?string $trackingCode);
}
