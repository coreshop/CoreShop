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
     * @var int
     */
    protected $decimalFactor;

    /**
     * @var int
     */
    protected $decimalPrecision;

    /**
     * @param TrackingExtractorInterface $extractor
     * @param int                        $decimalFactor
     * @param int                        $decimalPrecision
     */
    public function __construct(TrackingExtractorInterface $extractor, int $decimalFactor, int $decimalPrecision)
    {
        $this->extractor = $extractor;
        $this->decimalFactor = $decimalFactor;
        $this->decimalPrecision = $decimalPrecision;
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
         * @var ProposalInterface $object
         */
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
            ]
        );
    }

    /**
     * @param int $amount
     *
     * @return int
     */
    protected function parseAmount($amount)
    {
        return (int)round((round($amount / $this->decimalFactor, $this->decimalPrecision)), 0);
    }
}
