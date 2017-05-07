<?php

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

interface OrderDocumentInterface extends ResourceInterface, PimcoreModelInterface
{
    /**
     * @return string
     */
    public static function getDocumentType();

    /**
     * @return OrderInterface
     */
    public function getOrder();

    /**
     * @param OrderInterface $order
     */
    public function setOrder($order);

    /**
     * @return \DateTime
     */
    public function getDocumentDate();

    /**
     * @param \DateTime $documentDate
     */
    public function setDocumentDate($documentDate);

    /**
     * @return string
     */
    public function getDocumentNumber();

    /**
     * @param string $documentNumber
     */
    public function setDocumentNumber($documentNumber);

    /**
     * @return OrderDocumentItemInterface[]
     */
    public function getItems();

    /**
     * @param OrderDocumentItemInterface[] $items
     * @return mixed
     */
    public function setItems($items);

    /**
     * @return mixed
     */
    public function getRenderedAsset();

    /**
     * @param $renderedAsset
     */
    public function setRenderedAsset($renderedAsset);
}