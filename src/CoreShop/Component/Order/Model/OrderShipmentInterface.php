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

declare(strict_types=1);

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
