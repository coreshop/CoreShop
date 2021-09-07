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
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Product\ProductTaxCalculatorFactoryInterface;
use CoreShop\Component\Pimcore\BatchProcessing\DataObjectBatchListing;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20210727062209 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema): void
    {
        $class = $this->container->getParameter('coreshop.model.order_item_unit.class');

        if (class_exists($class)) {
            return;
        }

        $this->write('Create OrderItemUnit Class');
        $jsonFile = $this->container->get('kernel')->locateResource('@CoreShopOrderBundle/Resources/install/pimcore/classes/CoreShopOrderItemUnit.json');
        $this->container->get('coreshop.class_installer')->createClass($jsonFile, 'CoreShopOrderItemUnit');

        $orders = $this->container->get('coreshop.repository.order')->getList();

        if ($orders->getTotalCount() === 0) {
            return;
        }

        $batchListing = new DataObjectBatchListing($orders, 50);

        /**
         * @var OrderInterface $order
         */
        foreach ($batchListing as $order) {
            /**
             * @var OrderItemInterface $item
             */
            foreach ($order->getItems() as $item) {
                for ($i = 0; $i < $item->getQuantity(); $i++) {
                    $this->container->get('coreshop.factory.order_item_unit')->createForItem($item);
                }

                /**
                 * @var StoreInterface $store
                 */
                $store = $order->getStore();

                $taxCalculator = $this->container->get(ProductTaxCalculatorFactoryInterface::class)->getTaxCalculator(
                    $item->getProduct(), $order->getShippingAddress()
                );

                if ($taxCalculator instanceof TaxCalculatorInterface) {
                    if ($store->getUseGrossPrice()) {
                        $itemPrice = $item->getItemPrice(true);
                        $totalTaxAmount = $taxCalculator->getTaxesAmountFromGross((int)round($itemPrice * $item->getQuantity()));
                    }
                    else {
                        $itemPrice = $item->getItemPrice(false);
                        $totalTaxAmount = $taxCalculator->getTaxesAmount((int)round($itemPrice * $item->getQuantity()));
                    }

                    $splitTaxes = $this->container->get('coreshop.proportional_integer_distributor')->distribute($totalTaxAmount, (int)$item->getQuantity());

                    foreach ($item->getUnits() as $unit) {
                        if (0 === $splitTaxes[$i]) {
                            continue;
                        }

                        $unit->setKey($i+1);
                        $unit->setParent($item);
                        $unit->setPublished(true);

                        $unit->setSubtotal($itemPrice + $splitTaxes[$i], true);
                        $unit->setSubtotal($itemPrice, false);

                        $unit->setTotal($itemPrice + $splitTaxes[$i], true);
                        $unit->setTotal($itemPrice, false);

                        $unit->save();

                        $i++;
                    }
                }
                else {
                    $itemPrice = $item->getItemPrice(true);
                    $i = 0;

                    foreach ($item->getUnits() as $unit) {
                        $unit->setKey($i+1);
                        $unit->setParent($item);
                        $unit->setPublished(true);

                        $unit->setSubtotal($itemPrice, true);
                        $unit->setSubtotal($itemPrice, false);

                        $unit->setTotal($itemPrice, true);
                        $unit->setTotal($itemPrice, false);

                        $i++;
                    }
                }
            }
        }
    }

    public function down(Schema $schema): void
    {

    }
}
