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

declare(strict_types=1);

namespace CoreShop\Component\Order\Model;

use Carbon\Carbon;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;

abstract class OrderShipment extends AbstractPimcoreModel implements OrderShipmentInterface
{
    public static function getDocumentType(): string
    {
        return 'shipment';
    }

    public function getRenderedAsset()
    {
        return $this->getProperty('rendered_asset');
    }

    public function setRenderedAsset($renderedAsset)
    {
        $this->setProperty('rendered_asset', 'asset', $renderedAsset);
    }

    public function getDocumentDate(): ?Carbon
    {
        return $this->getShipmentDate();
    }

    public function setDocumentDate(?Carbon $documentDate)
    {
        return $this->setShipmentDate($documentDate);
    }

    public function getDocumentNumber(): ?string
    {
        return $this->getShipmentNumber();
    }

    public function setDocumentNumber(?string $documentNumber)
    {
        return $this->setShipmentNumber($documentNumber);
    }
}
