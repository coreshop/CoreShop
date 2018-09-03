<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Tracking\Extractor;

use CoreShop\Component\Order\Model\AdjustmentInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Tracking\Extractor\TrackingExtractorInterface;

class OrderExtractor implements TrackingExtractorInterface
{
    /**
     * @var TrackingExtractorInterface
     */
    private $extractor;

    /**
     * @param TrackingExtractorInterface $extractor
     */
    public function __construct(TrackingExtractorInterface $extractor)
    {
        $this->extractor = $extractor;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($object)
    {
        return $object instanceof ProposalInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function updateMetadata($object, $data = []): array
    {
        /**
         * @var $object ProposalInterface
         */
        $items = [];

        foreach ($object->getItems() as $item) {
            $items[] = $this->extractor->updateMetadata($item);
        }

        return array_merge(
            $data,
            [
                'id' => $object->getId(),
                'affiliation' => $object->getTotal() / 100,
                'total' => $object->getTotal() / 100,
                'tax' => $object->getTotalTax() / 100,
                'shipping' => $object->getAdjustmentsTotal(AdjustmentInterface::SHIPPING) / 100,
                'discount' => $object->getAdjustmentsTotal(AdjustmentInterface::CART_PRICE_RULE) / 100,
                'currency' => $object->getCurrency()->getIsoCode(),
                'items' => $items,
            ]
        );
    }
}