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

namespace CoreShop\Component\Core\Tracking\Extractor;

use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Tracking\Extractor\TrackingExtractorInterface;

class OrderItemExtractor implements TrackingExtractorInterface
{
    protected int $decimalFactor;

    public function __construct(int $decimalFactor)
    {
        $this->decimalFactor = $decimalFactor;
    }

    public function supports($object): bool
    {
        return $object instanceof OrderItemInterface;
    }

    public function updateMetadata($object, $data = []): array
    {
        /**
         * @var OrderItemInterface $object
         */
        $product = $object->getProduct();
        $categories = [];

        if ($product instanceof ProductInterface) {
            $categories = $product->getCategories();
        }

        $proposal = null;

        if ($object instanceof OrderItemInterface) {
            $proposal = $object->getOrder();
        }

        return array_merge($data, [
            'id' => $object->getId(),
            'sku' => $product instanceof ProductInterface ? $product->getSku() : '',
            'name' => $product instanceof PurchasableInterface ? $product->getName() : '',
            'category' => (is_array($categories) && count($categories) > 0) ? $categories[0]->getName() : '',
            'price' => $object->getItemPrice() / $this->decimalFactor,
            'quantity' => $object->getQuantity(),
            'currency' => $proposal ? $proposal->getCurrency()->getIsoCode() : '',
        ]);
    }
}
