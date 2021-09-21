<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Core\Order\Processor;

use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Taxation\Collector\TaxCollectorInterface;
use Pimcore\Model\DataObject\Fieldcollection;
use Webmozart\Assert\Assert;

final class CartItemTaxProcessor implements CartProcessorInterface
{
    private TaxCollectorInterface $taxCollector;

    public function __construct(
        TaxCollectorInterface $taxCollector,
    ) {
        $this->taxCollector = $taxCollector;
    }

    public function process(OrderInterface $cart): void
    {
        $store = $cart->getStore();

        /**
         * @var StoreInterface $store
         */
        Assert::isInstanceOf($store, StoreInterface::class);

        /**
         * @var OrderItemInterface $item
         */
        foreach ($cart->getItems() as $item) {
            $fieldCollection = new Fieldcollection();

            foreach ($item->getUnits() as $unit) {
                if (!$unit->getTaxes()) {
                    continue;
                }

                $fieldCollection->setItems(
                    $this->taxCollector->mergeTaxes(
                        $unit->getTaxes()->getItems(),
                        $fieldCollection->getItems()
                    )
                );
            }

            $item->setTaxes($fieldCollection);
        }
    }
}
