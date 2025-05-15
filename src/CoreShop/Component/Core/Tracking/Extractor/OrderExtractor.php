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

namespace CoreShop\Component\Core\Tracking\Extractor;

use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\Model\AdjustmentInterface;
use CoreShop\Component\Tracking\Extractor\TrackingExtractorInterface;

class OrderExtractor implements TrackingExtractorInterface
{
    public function __construct(
        protected TrackingExtractorInterface $extractor,
        protected int $decimalFactor,
        protected int $decimalPrecision,
    ) {
    }

    public function supports($object): bool
    {
        return $object instanceof OrderInterface;
    }

    public function updateMetadata($object, $data = []): array
    {
        $items = [];

        foreach ($object->getItems() as $item) {
            $items[] = $this->extractor->updateMetadata($item);
        }

        return array_merge(
            $data,
            [
                'id' => $object->getId(),
                'affiliation' => $this->parseAmount($object->getTotal()),
                'total' => $this->parseAmount($object->getTotal()),
                'subtotal' => $this->parseAmount($object->getSubtotal()),
                'totalTax' => $this->parseAmount($object->getTotalTax()),
                'shipping' => $this->parseAmount($object->getAdjustmentsTotal(AdjustmentInterface::SHIPPING)),
                'discount' => $this->parseAmount($object->getAdjustmentsTotal(AdjustmentInterface::CART_PRICE_RULE)),
                'currency' => $object->getCurrency()->getIsoCode(),
                'items' => $items,
            ],
        );
    }

    protected function parseAmount(int $amount): int
    {
        return (int) round((round($amount / $this->decimalFactor, $this->decimalPrecision)), 0);
    }
}
