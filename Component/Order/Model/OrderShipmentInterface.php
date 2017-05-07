<?php

namespace CoreShop\Component\Order\Model;

interface OrderShipmentInterface extends OrderDocumentInterface
{
    /**
     * @return \DateTime
     */
    public function getShipmentDate();

    /**
     * @param \DateTime $shipmentDate
     *
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
     * @return mixed
     */
    public function getCarrier();

    /**
     * @param $carrier
     * @return mixed
     */
    public function setCarrier($carrier);

    /**
     * @return string
     */
    public function getTrackingCode();

    /**
     * @param string $trackingCode
     */
    public function setTrackingCode($trackingCode);

    /**
     * @return float
     */
    public function getWeight();

    /**
     * @param float $weight
     */
    public function setWeight($weight);
}