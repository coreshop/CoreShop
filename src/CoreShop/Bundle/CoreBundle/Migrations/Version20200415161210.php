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

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\OrderSaleStates;
use CoreShop\Component\Pimcore\BatchProcessing\BatchListing;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Pimcore\Model\DataObject\Service;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20200415161210 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema): void
    {
        $cartClass = 'Pimcore\Model\DataObject\CoreShopCart\Listing';

        if (!class_exists($cartClass)) {
            return;
        }

        $cartList = new $cartClass();
        $batchList = new BatchListing($cartList, 50);

        $withTaxFields = [
            'total',
            'subtotal',
        ];

        $normalFields = [
            'shippingTaxRate',
            'taxes',
            'pimcoreAdjustmentTotalNet',
            'pimcoreAdjustmentTotalGross',
            'adjustmentItems',
            'store',
            'localeCode',
            'carrier',
            'paymentProvider',
            'paymentSettings',
            'comment',
            'additionalData',
            'priceRuleItems',
            'customer',
            'shippingAddress',
            'invoiceAddress',
            'currency'
        ];

        $fieldsNotMigrated = [];

        foreach ($batchList as $cart) {
            /**
             * @var OrderInterface $order
             */
            $order = $this->container->get('coreshop.factory.order')->createNew();

            $order->setSaleState(OrderSaleStates::STATE_CART);
            $order->setParent(Service::createFolderByPath($this->container->getParameter('coreshop.folder.cart')));
            $order->setKey($cart->getKey());
            $order->setPublished(true);

            foreach ($normalFields as $field) {
                $getterFrom = 'get' . ucfirst($field);
                $setterTo = 'set' . ucfirst($field);

                if (!method_exists($order, $getterFrom)) {
                    $fieldsNotMigrated[] = $field;
                    continue;
                }

                if (!method_exists($order, $setterTo)) {
                    $fieldsNotMigrated[] = $field;
                    continue;
                }

                $value = $cart->{$getterFrom}();
                $order->{$setterTo}($value);
            }

            foreach ($withTaxFields as $field) {
                foreach ([true, false] as $withTax) {
                    $getterFrom = 'get'.ucfirst($field);
                    $setterTo = 'set'.ucfirst($field);

                    if (!method_exists($order, $getterFrom)) {
                        $fieldsNotMigrated[] = $field;
                        continue;
                    }

                    if (!method_exists($order, $setterTo)) {
                        $fieldsNotMigrated[] = $field;
                        continue;
                    }

                    $value = $cart->{$getterFrom}($withTax);
                    $order->{$setterTo}($value, $withTax);
                }
            }

            $order->save();

            $order->setItems($this->migrateOrderItems($order, $cart->getItems()));
            $order->save();

            $this->container->get(CartManagerInterface::class)->persistCart($order);
        }
    }

    protected function migrateOrderItems(OrderInterface $order, array $items): array
    {
        $withTaxFields = [
            'total',
            'itemPrice',
            'itemRetailPrice',
            'itemDiscount',
            'ItemDiscountPrice',
        ];

        $normalFields = [
            'product',
            'quantity',
            'defaultUnitQuantity',
            'isGiftItem',
            'digitalProduct',
            'unitDefinition',
            'itemWholesalePrice',
            'itemTax',
            'taxes',
            'pimcoreAdjustmentsTotalNet',
            'pimcoreAdjustmentsTotalGross',
            'adjustmentItems',
        ];

        $orderItems = [];

        foreach ($items as $cartItem)
        {
            /**
             * @var OrderItemInterface $orderItem
             */
            $orderItem = $this->container->get('coreshop.factory.order_item')->createNew();
            $orderItem->setKey($cartItem->getKey());
            $orderItem->setParent($order);
            $orderItem->setPublished(true);

            foreach ($normalFields as $field) {
                $getterFrom = 'get' . ucfirst($field);
                $setterTo = 'set' . ucfirst($field);

                if (!method_exists($orderItem, $getterFrom)) {
                    continue;
                }

                if (!method_exists($orderItem, $setterTo)) {
                    continue;
                }

                $value = $cartItem->{$getterFrom}();
                $orderItem->{$setterTo}($value);
            }

            foreach ($withTaxFields as $field) {
                foreach ([true, false] as $withTax) {
                    $getterFrom = 'get'.ucfirst($field);
                    $setterTo = 'set'.ucfirst($field);

                    if (!method_exists($orderItem, $getterFrom)) {
                        continue;
                    }

                    if (!method_exists($orderItem, $setterTo)) {
                        continue;
                    }

                    $value = $cartItem->{$getterFrom}($withTax);
                    $orderItem->{$setterTo}($value, $withTax);
                }
            }

            $orderItem->save();

            $orderItems[] = $orderItem;
        }

        return $orderItems;
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
