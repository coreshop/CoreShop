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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\Exception\ImplementedByPimcoreException;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;

abstract class OrderShipmentItem extends AbstractPimcoreModel implements OrderShipmentItemInterface
{
    public function getDocument(): OrderDocumentInterface
    {
        $parent = $this->getParent();

        do {
            if ($parent instanceof OrderDocumentInterface) {
                return $parent;
            }

            $parent = $parent->getParent();
        } while ($parent !== null);

        throw new \InvalidArgumentException('Order Shipment could not be found!');
    }

    public function getTotal(bool $withTax = true): int
    {
        return $withTax ? $this->getTotalGross() : $this->getTotalNet();
    }

    /**
     * @return void
     */
    public function setTotal(int $total, bool $withTax = true)
    {
        $withTax ? $this->setTotalGross($total) : $this->setTotalNet($total);
    }

    public function getConvertedTotal(bool $withTax = true): int
    {
        return $withTax ? $this->getConvertedTotalGross() : $this->getConvertedTotalNet();
    }

    /**
     * @return void
     */
    public function setConvertedTotal(int $convertedTotal, bool $withTax = true)
    {
        $withTax ? $this->setConvertedTotalGross($convertedTotal) : $this->setConvertedTotalNet($convertedTotal);
    }

    public function getTotalNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @return never
     */
    public function setTotalNet(int $totalNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getTotalGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @return never
     */
    public function setTotalGross(int $totalGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getConvertedTotalNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @return never
     */
    public function setConvertedTotalNet(int $convertedTotalNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getConvertedTotalGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @return never
     */
    public function setConvertedTotalGross(int $convertedTotalGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }
}
