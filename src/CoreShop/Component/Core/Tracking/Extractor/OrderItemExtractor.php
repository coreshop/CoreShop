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

namespace CoreShop\Component\Core\Tracking\Extractor;

use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Order\Model\ProposalItemInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Tracking\Extractor\TrackingExtractorInterface;

class OrderItemExtractor implements TrackingExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports($object)
    {
        return $object instanceof ProposalItemInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function updateMetadata($object, $data = []): array
    {
        /**
         * @var ProposalItemInterface $object
         */
        $product = $object->getProduct();
        $categories = [];

        if ($product instanceof ProductInterface) {
            $categories = $product->getCategories();
        }

        $proposal = null;

        if ($object instanceof CartItemInterface) {
            $proposal = $object->getCart();
        } elseif ($object instanceof OrderItemInterface) {
            $proposal = $object->getOrder();
        }

        return array_merge($data, [
            'id' => $object->getId(),
            'sku' => $product instanceof ProductInterface ? $product->getSku() : '',
            'name' => $product instanceof PurchasableInterface ? $product->getName() : '',
            'category' => (is_array($categories) && count($categories) > 0) ? $categories[0]->getName() : '',
            'price' => $object->getTotal() / 100,
            'quantity' => $object->getQuantity(),
            'currency' => $proposal ? $proposal->getCurrency()->getIsoCode() : '',
        ]);
    }
}
