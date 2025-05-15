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

use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Tracking\Extractor\TrackingExtractorInterface;

class OrderItemExtractor implements TrackingExtractorInterface
{
    public function __construct(
        protected int $decimalFactor,
    ) {
    }

    public function supports($object): bool
    {
        return $object instanceof OrderItemInterface;
    }

    public function updateMetadata($object, $data = []): array
    {
        $product = $object->getProduct();
        $categories = [];

        if ($product instanceof ProductInterface) {
            $categories = $product->getCategories();
        }

        $order = null;

        if ($object instanceof OrderItemInterface) {
            $order = $object->getOrder();
        }

        return array_merge($data, [
            'id' => $object->getId(),
            'sku' => $product instanceof ProductInterface ? $product->getSku() : '',
            'name' => $product instanceof PurchasableInterface ? $product->getName() : '',
            'category' => (is_array($categories) && count($categories) > 0) ? $categories[0]->getName() : '',
            'price' => $object->getItemPrice() / $this->decimalFactor,
            'quantity' => $object->getQuantity(),
            'currency' => $order ? $order->getCurrency()->getIsoCode() : '',
        ]);
    }
}
