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

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\Exception\ImplementedByPimcoreException;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;

class OrderShipment extends AbstractPimcoreModel implements OrderShipmentInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getDocumentType()
    {
        return 'shipment';
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder($order)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getRenderedAsset()
    {
        return $this->getProperty('rendered_asset');
    }

    /**
     * {@inheritdoc}
     */
    public function setRenderedAsset($renderedAsset)
    {
        $this->setProperty('rendered_asset', 'asset', $renderedAsset);
    }

    /**
     * {@inheritdoc}
     */
    public function getDocumentDate()
    {
        return $this->getShipmentDate();
    }

    /**
     * {@inheritdoc}
     */
    public function setDocumentDate($documentDate)
    {
        return $this->setShipmentDate($documentDate);
    }

    /**
     * {@inheritdoc}
     */
    public function getDocumentNumber()
    {
        return $this->getShipmentNumber();
    }

    /**
     * {@inheritdoc}
     */
    public function setDocumentNumber($documentNumber)
    {
        return $this->setShipmentNumber($documentNumber);
    }

    /**
     * {@inheritdoc}
     */
    public function getShipmentDate()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setShipmentDate($invoiceDate)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getShipmentNumber()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setShipmentNumber($invoiceNumber)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setState($state)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setItems($items)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getTrackingCode()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setTrackingCode($trackingCode)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }
}
