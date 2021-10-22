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
use CoreShop\Component\Order\OrderSaleStates;
use CoreShop\Component\Pimcore\BatchProcessing\BatchListing;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20200415153638 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema): void
    {
        $orderList = $this->container->get('coreshop.repository.order')->getList();
        $batchList = new BatchListing($orderList, 50);

        $fieldMap = [
            'paymentTotal' => 'convertedPaymentTotal',
            'totalNet' => 'convertedTotalNet',
            'totalGross' => 'convertedTotalGross',
            'subtotalNet' => 'convertedSubtotalNet',
            'subtotalGross' => 'convertedSubtotalGross',
            'shippingTaxRate' => 'convertedShippingTaxRate',
            'taxes' => 'convertedTaxes',
            'pimcoreAdjustmentTotalNet' => 'convertedPimcoreAdjustmentTotalNet',
            'pimcoreAdjustmentTotalGross' => 'convertedPimcoreAdjustmentTotalGross',
            'adjustmentItems' => 'convertedAdjustmentItems',

            'baseTotalNet' => 'totalNet',
            'baseTotalGross' => 'totalGross',
            'baseSubtotalNet' => 'subtotalNet',
            'baseSubtotalGross' => 'subtotalGross',
            'baseShippingTaxRate' => 'shippingTaxRate',
            'baseTaxes' => 'taxes',
            'basePimcoreAdjustmentTotalNet' => 'pimcoreAdjustmentTotalNet',
            'basePimcoreAdjustmentTotalGross' => 'pimcoreAdjustmentTotalGross',
            'baseAdjustmentItems' => 'adjustmentItems',
        ];

        $fieldsNotMigrated = [];

        foreach ($batchList as $order) {
            if (!$order instanceof OrderInterface) {
                continue;
            }

            $order->setSaleState(OrderSaleStates::STATE_ORDER);

            foreach ($fieldMap as $from => $to) {
                $getterFrom = 'get' . ucfirst($from);
                $setterTo = 'set' . ucfirst($to);

                if (!method_exists($order, $getterFrom)) {
                    $fieldsNotMigrated[$from] = $to;
                    continue;
                }

                if (!method_exists($order, $setterTo)) {
                    $fieldsNotMigrated[$from] = $to;
                    continue;
                }

                $value = $order->{$getterFrom}();

                $order->{$setterTo}($value);
            }

            $order->save();
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
