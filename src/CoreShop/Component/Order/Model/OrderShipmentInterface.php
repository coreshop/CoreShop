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

namespace CoreShop\Component\Order\Model;

interface OrderShipmentInterface extends OrderDocumentInterface
{
    /**
     * @return \DateTime
     */
    public function getShipmentDate();

    /**
     * @param \DateTime $shipmentDate
     */
    public function setShipmentDate($shipmentDate);

    /**
     * @return string
     */
    public function getShipmentNumber();

    /**
     * @param string $shipmentNumber
     */
    public function setShipmentNumber($shipmentNumber);

    /**
     * @return string
     */
    public function getTrackingCode();

    /**
     * @param string $trackingCode
     */
    public function setTrackingCode($trackingCode);
}
